<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\SubscriptionRepository;
use App\Stripe\PaymentGateway;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param SubscriptionRepository $subscriptions
     * @param PaymentGateway         $gateway
     */
    public function __construct(SubscriptionRepository $subscriptions, PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
        $this->subscriptions = $subscriptions;
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

    /**
     * @Route("/subscriptions/{id}/cancel", name="subsciption.user.cancel", methods={"POST"})
     *
     * @param Subscription $subscription
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancel2(Subscription $subscription)
    {
        $id = $subscription->getStripeId();

        try {
            $this->gateway->cancel($id);
            $subscription->setStatus('pending');
            $this->subscriptions->save($subscription);
        } catch (\Exception $e) {
//            $this->logger->error($e->getMessage());
            $this->addFlash('errors', $e->getMessage());
        }

        return $this->redirectToRoute('admin.subscriptions.list');
    }

    /**
     * @Route("/subscription/cancel", name="subscription.cancel", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse|Response
     *
     */
    public function cancel(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $subscription = $this->gateway->cancel($data['subscriptionId']);

        $subscriptionInDB = $this->subscriptions->findOneBy([
            'stripeId' => $subscription->id,
        ]);

        $subscriptionInDB->setStatus('pending');
        $this->subscriptions->save($subscriptionInDB);

        return $this->json([
           'content' => '$rendered',
        ]);

    }
}
