<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Stripe\Event;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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
