<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Stripe\PaymentGateway;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(EntityManagerInterface $em, PaymentGateway $gateway)
    {
        $this->em      = $em;
        $this->gateway = $gateway;
    }

    /**
     * @Route("/subscribe", name="subscribe", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     * @throws ExceptionInterface
     */
    public function subscribe(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $subscription = $this->gateway->subscribe($data);

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

        $serialized_subscription = $serializer->serialize($new_subscription, 'json', ['ignored_attributes' => ['customer']]);

        return $this->json($serializer->decode($serialized_subscription, 'json'));
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

}
