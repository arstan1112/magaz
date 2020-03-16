<?php


namespace App\Message;


use App\Entity\User;
use Stripe\Subscription;

class SubscribeToProduct
{
    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * @var int
     */
    private $userId;

    public function __construct(Subscription $subscription, int $userId)
    {
        $this->subscription = $subscription;
        $this->userId = $userId;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

}