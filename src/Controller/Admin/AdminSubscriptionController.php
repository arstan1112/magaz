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
//        $user = $this->em->getRepository(User::class)->find($this->getUser()->getId());
//        dump($this->getUser());
//        die();

        $subscriptions = $this->subscriptions->findBy([
            'customer' => $this->getUser(),
        ]);

        return $this->render('admin/subscription/list.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * @Route("/subscriptions/{id}/cancel", name="users.cancel", methods={"POST"})
     */
    public function cancel2(Subscription $subscription)
    {
        $id = $subscription->getStripeId();

        try {
            $this->gateway->cancel($id);

            // $subscription->setStatus('pending');

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
     * @throws ApiErrorException
     */
    public function cancel(Request $request)
    {
//        $subscriptionId;
//        \Stripe\Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');

        $data = json_decode($request->getContent(), true);

        $subscription = $this->gateway->cancel($data['subscriptionId']);

        // Subscription
        // status: active, pending, cancelled

//        $subscription = \Stripe\Subscription::retrieve(
//            $data['subscriptionId']
//        );
//        $subscription->delete();

//        $subscriptionInDB = $this->em->getRepository(Subscription::class)->findWithStripeId($subscription->id);

        $subscriptionInDB = $this->subscriptions->find($subscription->id);

//        dump($subscriptionInDB[0]);
//        die();
//        $this->em->remove($subscriptionInDB[0]);
//        $this->em->flush();

//        $subscriptions = $this->em->getRepository(Subscription::class)->findWithUserId($this->getUser()->getId());

        $rendered = $this->renderView('admin/subscription/table.html.twig', [
           'subscriptions' => $subscriptions,
        ]);

        return $this->json([
           'content' => $rendered,
        ]);

//        return $this->render('admin/subscription/list.html.twig', [
//            'subscriptions' => $subscriptions,
//        ]);
//        return $this->json($subscription);
    }
}
