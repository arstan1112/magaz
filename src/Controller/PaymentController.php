<?php

namespace App\Controller;

use Stripe\Event;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="payment")
     *
     * @throws ApiErrorException
     */
    public function show()
    {
        \Stripe\Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');

        $intent = \Stripe\PaymentIntent::create([
            'amount'   => 1055,
            'currency' => 'usd',
        ]);

        return $this->render('payment/payment.html.twig', [
            'clientSecret' => $intent->client_secret,
        ]);
    }

    /**
     * @Route("/charge/{value}", name="charge")
     *
     * @param Request $request
     * @param int $value
     *
     * @return JsonResponse
     *
     * @throws ApiErrorException
     */
    public function charge(Request $request, int $value)
    {
        \Stripe\Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');
        $data = $request->getContent();

        $intent = \Stripe\PaymentIntent::create([
            'amount'   => 1055,
            'currency' => 'usd',
        ]);

        return $this->json($intent);
    }

//    /**
//     * @Route("/subscribe", name="subscribe")
//     *
//     * @return Response
//     *
//     */
//    public function subscribe()
//    {
//
//        return $this->render('payment/subscription.html.twig');
//    }

    /**
     * @Route("/subscribe", name="subscribe", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ApiErrorException
     * @throws \Exception
     */
    public function createSubscription(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');
        $data = json_decode($request->getContent(), true);
//        dump($data['payment_method']);
//        dump($data['email']);
//        dump($data);
//        die();

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
//                    'plan' => 'plan_GfQD3TLBwZx5uQ',
                    'plan' => $data['pricing_plan'],
                ],
            ],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        return $this->json($subscription);

    }

    /**
     * @Route("/success")
     *
     * @return Response
     */
    public function success()
    {
        return $this->render('payment/success.html.twig', [
            'operation_name' => 'subscription',
        ]);
    }

//    /**
//     * @Route("/create-product")
//     *
//     * @throws ApiErrorException
//     */
//    public function createProduct()
//    {
//        \Stripe\Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');
//        $product = \Stripe\Product::create([
//            'name' => 'MySoft Platform',
//            'type' => 'service',
//        ]);
//
//        $plan = \Stripe\Plan::create([
//            'currency' => 'usd',
//            'interval' => 'month',
//            'product' => $product->id,
//            'nickname' => 'MySoft Pro Plan',
//            'amount' => 300,
//        ]);
//    }

    /**
     * @Route("/webhook", name="webhook")
     *
     * @param Request $request
     */
    public function hook(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');

        $endpoint_secret = 'whsec_NjDvwBC3LRE6ek0R4ph9heXM4yKrojZa';

        $payload = @file_get_contents('php://input');
        $sig_header = $request->server->get('HTTP_STRIPE_SIGNATURE');
        $event = null;

        try {
//            $event = StripeEvent::constructFrom(
            $event = \Stripe\Webhook::constructEvent(
//                json_decode($payload, true)
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
//            http_response_code(400);
            http_response_code(401);

            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
//            http_response_code(400);
            http_response_code(402);
            exit();
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;

            case 'payment_method.attached':
                $paymentMethod = $event->data->object;
                $this->handlePaymentMethodAttached($paymentMethod);
                break;

            default:
                http_response_code(208);
                exit();
        }

        http_response_code(200);
    }

    /**
     * @param object $paymentIntent
     * @return void
     */
    public function handlePaymentIntentSucceeded(object $paymentIntent)
    {
        http_response_code(201);
        exit();
    }

    /**
     * @param object $paymentMethod
     * @return void
     */
    public function handlePaymentMethodAttached(object $paymentMethod)
    {
        http_response_code(202);
        exit();
    }
}
