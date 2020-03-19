<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\ProductType;
use App\Message\CancelSubscription;
use App\Repository\SubscriptionRepository;
use App\Stripe\PaymentGateway;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
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

class AdminSubscriptionController extends AbstractController
{
    /**
     * @var PaymentGateway
     */
    private $gateway;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptions;

    /**
     * @var LoggerInterface
     */
    private $stripeLogger;

    /**
     * @param SubscriptionRepository $subscriptions
     * @param PaymentGateway $gateway
     * @param LoggerInterface $stripeLogger
     */
    public function __construct(SubscriptionRepository $subscriptions, PaymentGateway $gateway, LoggerInterface $stripeLogger)
    {
        $this->gateway = $gateway;
        $this->subscriptions = $subscriptions;
        $this->stripeLogger = $stripeLogger;
    }

    /**
     * @Route("/admin/subscription", name="admin.subscriptions.list")
     */
    public function list()
    {
//        $subscriptions = $this->subscriptions->findBy([
//            'customer' => $this->getUser(),
//        ], ['currentPeriodStartAt' => 'DESC']);
        $subscriptions = $this->subscriptions->findActiveByUserId($this->getUser());

        return $this->render('admin/subscription/list.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }

//    /**
//     * @Route("/subscriptions/{id}/cancel", name="subsciption.user.cancel", methods={"POST"})
//     *
//     * @param Subscription $subscription
//     *
//     * @return \Symfony\Component\HttpFoundation\RedirectResponse
//     */
//    public function cancel2(Subscription $subscription)
//    {
//        $id = $subscription->getStripeId();
//
//        try {
//            $this->gateway->cancel($id);
//            $subscription->setStatus('pending');
//            $this->subscriptions->save($subscription);
//        } catch (\Exception $e) {
////            $this->logger->error($e->getMessage());
//            $this->addFlash('errors', $e->getMessage());
//        }
//
//        return $this->redirectToRoute('admin.subscriptions.list');
//    }

    /**
     * @Route("/subscription/cancel", name="subscription.cancel", methods={"POST"})
     *
     * @param Request $request
     *
     * @param MessageBusInterface $messageBus
     * @return JsonResponse|Response
     */
    public function cancel(Request $request, MessageBusInterface $messageBus)
    {
        $data = json_decode($request->getContent(), true);

        $cancelMessage = new CancelSubscription($data['subscriptionId']);
        $envelope = new Envelope($cancelMessage, [
            new DelayStamp(1000)
        ]);
        $messageBus->dispatch($envelope);

//        try {
//            $subscription = $this->gateway->cancel($data['subscriptionId']);
//        } catch (\Exception $exception) {
//            $this->stripeLogger->error($exception->getMessage());
//
//            return $this->json([
//               'status' => 'error',
//               'message' => $exception->getMessage(),
//            ]);
//        }

        $subscriptionInDB = $this->subscriptions->findOneBy([
//            'stripeId' => $subscription->id,
            'stripeId' => $data['subscriptionId'],
        ]);

//        dump($subscriptionInDB);
        $subscriptionInDB->setStatus('pending');
        $this->subscriptions->save($subscriptionInDB);

        return $this->json([
            'status' => 'success',
//            '$subscription->id' => $subscription->id,
//            '$data[subscr__id]' => $data['subscriptionId'],
        ]);
    }
}
