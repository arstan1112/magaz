<?php


namespace App\Message;


use Stripe\Subscription;

class CancelSubscription
{
    /**
     * @var string
     */
    private $subscriptionId;

    public function __construct(string $subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * @return string
     */
    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }


}