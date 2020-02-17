<?php


namespace App\Service;

use phpDocumentor\Reflection\Types\This;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Stripe\{Exception\SignatureVerificationException, Stripe, PaymentIntent, Webhook};


class StripeWebHook
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MailerService
     */
    private $mailer;

    /**
     * StripeWebHook constructor.
     * @param LoggerInterface $stripeLogger
     * @param MailerService $mailer
     */
    public function __construct(LoggerInterface $stripeLogger, MailerService $mailer)
    {
        $this->logger = $stripeLogger;
        $this->mailer = $mailer;
    }

    /**
     * @param $header
     *
     * @return JsonResponse
     */
    public function hook($header)
    {
        Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');

        $endpoint_secret = 'whsec_AuV9eacljG8Cztg2KvGrvzeYGEsM9uEa';

        $payload = @file_get_contents('php://input');
        $sig_header = $header;
        $event = null;

        try {
//            $event = StripeEvent::constructFrom(
            $event = Webhook::constructEvent(
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
            case 'payment_method.attached':
                $paymentMethod = $event->data->object;
                $this->handlePaymentMethodAttached($paymentMethod);
                break;

            case 'customer.created':
                $createdCustomer = $event->data->object;
                $this->handleCustomerCreated($createdCustomer);
                break;

            case 'invoice.created':
                $createdInvoice = $event->data->object;
                return $this->handleInvoiceCreated($createdInvoice);
                break;

            case 'customer.subscription.created':
                $createdCustomerSubscription = $event->data->object;
                $this->handleCustomerSubscriptionCreated($createdCustomerSubscription);
                break;

            case 'payment_intent.created':
                $createdPaymentIntent = $event->data->object;
                $this->handlePaymentIntentCreated($createdPaymentIntent);
                break;

            case 'invoice.payment_succeeded':
                $succeededPaymentInvoice = $event->data->object;
                $this->handleInvoicePaymentSucceeded($succeededPaymentInvoice);
                break;

            case 'charge.succeeded':
                $succeededCharge = $event->data->object;
                $this->handleChargeSucceeded($succeededCharge);
                break;

            case 'payment_intent.succeeded':
                $succeededPaymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($succeededPaymentIntent);
                break;

            case 'invoice.finalized':
                $finalizedInvoice = $event->data->object;
                $this->handleInvoiceFinalized($finalizedInvoice);
                break;

            case 'customer.updated':
                $updatedCustomer = $event->data->object;
                $this->handleCustomerUpdated($updatedCustomer);
                break;

            case 'invoice.upcoming':
                $invoiceUpcoming = $event->data->object;
                $this->handleInvoiceUpcoming($invoiceUpcoming);
                break;

            case 'customer.subscription.deleted':
                $deletedSubscription = $event->data->object;
                return $this->handleSubscriptionDeleted($deletedSubscription);
                break;

            default:
                http_response_code(220);
                exit();
        }

        http_response_code(200);
//        return;
    }

    /**
     * @param $paymentMethod
     * @return void
     */
    public function handlePaymentMethodAttached($paymentMethod)
    {
        http_response_code(201);
        exit();
    }

    /**
     * @param $createdCustomer
     */
    public function handleCustomerCreated($createdCustomer)
    {
        http_response_code(202);
        exit();
    }

    /**
     * @param $createdInvoice
     *
     * @return mixed
     */
    public function handleInvoiceCreated($createdInvoice)
    {
        $this->logger->info('Invoice created for Customer ' .$createdInvoice->customer. ' at: '. $createdInvoice->invoice_pdf);
        $this->mailer->send('You can get your invoice at ' .$createdInvoice->invoice_pdf, $createdInvoice->customer_email);
//        $this->mailer->send('You can get your invoice at ' .$createdInvoice->invoice_pdf, 'stan.lee.mag@yandex.com');
        return $createdInvoice;
    }

    /**
     * @param $createdCustomerSubscription
     */
    public function handleCustomerSubscriptionCreated($createdCustomerSubscription)
    {
        http_response_code(204);
        exit();
    }

    /**
     * @param $createdPaymentIntent
     */
    public function handlePaymentIntentCreated($createdPaymentIntent)
    {
        http_response_code(205);
        exit();
    }

    /**
     * @param $succeededPaymentInvoice
     */
    public function handleInvoicePaymentSucceeded($succeededPaymentInvoice)
    {
        http_response_code(206);
        exit();
    }

    /**
     * @param $succeededCharge
     */
    public function handleChargeSucceeded($succeededCharge)
    {
        http_response_code(207);
        exit();
    }

    /**
     * @param $succeededPaymentIntent
     * @return void
     */
    public function handlePaymentIntentSucceeded($succeededPaymentIntent)
    {
        http_response_code(208);
        exit();
    }

    /**
     * @param $finalizedInvoice
     */
    public function handleInvoiceFinalized($finalizedInvoice)
    {
        http_response_code(208);
        exit();
    }

    /**
     * @param $updatedCustomer
     */
    public function handleCustomerUpdated($updatedCustomer)
    {
        http_response_code(208);
        exit();
    }

    /**
     * @param $invoiceUpcoming
     */
    public function handleInvoiceUpcoming($invoiceUpcoming)
    {
        http_response_code(208);
        exit();
    }

    /**
     * @param $deletedSubscription
     *
     * @return mixed
     */
    public function handleSubscriptionDeleted($deletedSubscription)
    {
        $this->logger->info('Subscription with id: ' .$deletedSubscription->id .', for Customer: ' .$deletedSubscription->customer. ', is cancelled by Customer');
        $this->mailer->send('Your subscription ' . $deletedSubscription->id . ' has been successfully cancelled', 'stan.lee.mag@yandex.com');
        return $deletedSubscription;
    }
}
