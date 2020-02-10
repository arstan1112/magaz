<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Stripe\PaymentGateway;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
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
     * @return string
     * @throws ApiErrorException
     * @throws \Exception
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function subscribe(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');
        $data = json_decode($request->getContent(), true);

        // This creates a new Customer and attaches the default PaymentMethod in one API call.
        $customer = \Stripe\Customer::create([
            'payment_method' => $data['payment_method'],
            'email' => $data['email'],
            'invoice_settings' => [
                'default_payment_method' => $data['payment_method']
            ]
        ]);

        $subscription = \Stripe\Subscription::create([
            'customer' => $customer,
            'items' => [
                [
                    'plan' => $data['pricing_plan'],
                ],
            ],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        $new_subscription = new Subscription();
        $new_subscription->setStripeId($subscription->id);
        $new_subscription->setCurrentPeriodEndAt(new \DateTime(strtotime($subscription->current_period_end)));
        $new_subscription->setCurrentPeriodStartAt(new \DateTime(strtotime($subscription->current_period_start)));
        $new_subscription->setNickname($subscription->plan->nickname);
        $new_subscription->setRegularity($subscription->plan->interval);
        $new_subscription->setAmount(($subscription->plan->amount)/100);
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
