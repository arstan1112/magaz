<?php


namespace App\MessageHandler;


use App\Message\CancelSubscription;
use App\Stripe\PaymentGateway;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CancelSubscriptionHandler implements MessageHandlerInterface
{
    /**
     * @var PaymentGateway
     */
    private $gateway;

    /**
     * @var LoggerInterface
     */
    private $stripeLogger;


    public function __construct(PaymentGateway $gateway, LoggerInterface $stripeLogger)
    {
        $this->gateway = $gateway;
        $this->stripeLogger = $stripeLogger;
    }

    public function __invoke(CancelSubscription $cancelSubscription)
    {
        try {
            $subscriptionId = $cancelSubscription->getSubscriptionId();
            $subscription = $this->gateway->cancel($subscriptionId);
            $this->stripeLogger->info('Subscription cancelling in handler');
        } catch (\Throwable $exception) {
            $this->stripeLogger->error($exception->getMessage());
        }


//        try {
////            $this->stripeLogger->info('Cancel subscription handler log');
//            $subscription = $this->gateway->cancel($subscriptionId);
//
////            throw new \Exception('Custom exception from CancelSub Message Handler');
//        } catch (\Exception $exception) {
////            $this->stripeLogger->info('Cancel subscription handler log');
////            $this->stripeLogger->error($exception->getMessage());
//
////            return $this->json([
////                'status' => 'error',
////                'message' => $exception->getMessage(),
////            ]);
//        }

    }


}