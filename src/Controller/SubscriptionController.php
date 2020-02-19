<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Stripe\PaymentGateway;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SubscriptionController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PaymentGateway
     */
    private $gateway;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $em, PaymentGateway $gateway, LoggerInterface $stripeLogger)
    {
        $this->em      = $em;
        $this->gateway = $gateway;
        $this->logger  = $stripeLogger;
    }

    /**
     * @Route("/subscribe", name="subscribe", methods={"POST"})
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws \Exception
     * @throws ExceptionInterface
     */
    public function subscribe(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $subscription = $this->gateway->subscribe($data);
        } catch (\Exception $e) {
            $this->logger->warning(
                'Stripe subscription initialization for user '
                .$this->getUser()->getUsername().
                ' failed with Exception: '. $e->getMessage()
            );
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        $new_subscription = new Subscription();
        $new_subscription->setStripeId($subscription->id);
        $new_subscription->setCurrentPeriodEndAt(new \DateTime(strtotime($subscription->current_period_end)));
        $new_subscription->setCurrentPeriodStartAt(new \DateTime(strtotime($subscription->current_period_start)));
        $new_subscription->setNickname($subscription->plan->nickname);
        $new_subscription->setRegularity($subscription->plan->interval);
        $new_subscription->setAmount(($subscription->plan->amount)/100);
        $new_subscription->setStatus('active');
        $new_subscription->setCustomer($this->getUser());

        $this->em->persist($new_subscription);
        $this->em->flush();

        $normalizer = new ObjectNormalizer();
        $encoder    = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);

        $serializedNewSubscriptionObject = $serializer->serialize($new_subscription, 'json', ['ignored_attributes' => ['customer']]);

        return $this->json($serializer->decode($serializedNewSubscriptionObject, 'json'));
    }

    /**
     * @Route("/success")
     *
     * @return Response
     */
    public function success()
    {
        return $this->render('subscription/success.html.twig', [
            'operation_name' => 'subscription',
        ]);
    }

    /**
     * @Route("/failure/{message}")
     *
     * @param string $message
     *
     * @return Response
     */
    public function failure(string $message)
    {
        return $this->render('subscription/failure.html.twig', [
            'operation_name' => 'subscription',
            'message' => $message,
        ]);
    }
}
