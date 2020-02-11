<?php

namespace App\Controller;

use App\Stripe\PaymentGateway;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\{Exception\SignatureVerificationException, Stripe, PaymentIntent};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PaymentController extends AbstractController
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
     * @Route("/payment", name="payment")
     *
     */
    public function show()
    {
        $intent = $this->gateway->pay();

        return $this->render('payment/payment.html.twig', [
            'clientSecret' => $intent->client_secret,
        ]);
    }

    /**
     * @Route("/webhook", name="webhook")
     *
     * @param Request $request
     */
    public function hook(Request $request)
    {
        Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');

        $endpoint_secret = 'whsec_AuV9eacljG8Cztg2KvGrvzeYGEsM9uEa';

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
            http_response_code(410);
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

            case 'payment_intent.created':
                $this->handlePaymentIntentCreated();
                break;

            case 'customer.created':
                $this->handleCustomerCreated();
                break;

            case 'customer.subscription.created':
                $this->handleCustomerSubscriptionCreated();
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

    public function handlePaymentIntentCreated()
    {
        http_response_code(203);
        exit();
    }

    public function handleCustomerCreated()
    {
        http_response_code(204);
        exit();
    }

    public function handleCustomerSubscriptionCreated()
    {
        http_response_code(205);
        exit();
    }
}
