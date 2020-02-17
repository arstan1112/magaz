<?php

namespace App\Controller;

use App\Service\StripeWebHook;
use App\Stripe\PaymentGateway;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\{Exception\SignatureVerificationException, Stripe, PaymentIntent};
use phpDocumentor\Reflection\Types\This;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StripeWebHook
     */
    private $hook;

    /**
     * PaymentController constructor.
     * @param EntityManagerInterface $em
     * @param PaymentGateway $gateway
     * @param LoggerInterface $stripeLogger
     * @param StripeWebHook $hook
     */
    public function __construct(
        EntityManagerInterface $em,
        PaymentGateway         $gateway,
        LoggerInterface        $stripeLogger,
        StripeWebHook          $hook
    ) {
        $this->em      = $em;
        $this->gateway = $gateway;
        $this->logger  = $stripeLogger;
        $this->hook    = $hook;
    }

    /**
     * @Route("/payment", name="payment")
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
     *
     * @return JsonResponse
     */
    public function hook(Request $request)
    {
        $header = $request->server->get('HTTP_STRIPE_SIGNATURE');
        return $this->json($this->hook->hook($header));
    }

    /**
     * @Route("/mail")
     *
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse
     */
    public function mail(\Swift_Mailer $mailer)
    {

        dump(php_sapi_name());
        die();
        $name = 'Kyle';
        $message = (new \Swift_Message('Hi email'))
            ->setFrom('slee76058@gmail.com')
            ->setTo('stan.lee.mag@yandex.com')
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    ['name' => $name]
                )
            )
            ;
        $mailer->send($message);

        return $this->redirectToRoute('products.list');
    }
}
