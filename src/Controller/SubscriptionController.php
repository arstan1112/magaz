<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\User;
use App\Message\SubscribeToProduct;
use App\Stripe\PaymentGateway;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
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
    private $stripeLogger;

    public function __construct(EntityManagerInterface $em, PaymentGateway $gateway, LoggerInterface $stripeLogger)
    {
        $this->em      = $em;
        $this->gateway = $gateway;
        $this->stripeLogger  = $stripeLogger;
    }

    /**
     * @Route("/subscribe", name="subscribe", methods={"POST"})
     *
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return string
     */
    public function subscribe(Request $request, MessageBusInterface $messageBus)
    {
        $data = json_decode($request->getContent(), true);
//        dd($data);

        try {
            $subscription = $this->gateway->subscribe($data);
            $this->stripeLogger->info('[SubscriptionController.php] Subscription sent to Stripe');

        } catch (\Exception $e) {
            $this->stripeLogger->warning(
                '[SubscriptionController.php] Stripe subscription initialization for user '
                .$this->getUser()->getUsername().
                ' failed with Exception: '. $e->getMessage()
            );
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 302);
        }

        $currentUser = $this->em->getRepository(User::class)->find($this->getUser());
        $userId = $currentUser->getId();
//        $userId = 4;
        $subMessage = new SubscribeToProduct($subscription, $userId, $data['email']);
//        $envelope = new Envelope($subMessage, [
//            new DelayStamp(3000)
//        ]);

        $envelope = new Envelope($subMessage);
        $messageBus->dispatch($envelope);
        $this->stripeLogger->info('[SubscriptionController.php] Subscription sent to message handler');

        return $this->json([
            'status' => 'success',
            'message' => 'Subscription sent to Stripe and is handling by messenger handler',
        ]);
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
