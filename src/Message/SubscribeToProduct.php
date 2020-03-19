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

    /**
     * @var string
     */
    private $email;

    public function __construct(Subscription $subscription, int $userId, string $email)
    {
        $this->subscription = $subscription;
        $this->userId = $userId;
        $this->email = $email;
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

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

}