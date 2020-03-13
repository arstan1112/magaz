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
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $userId;

//    public function __construct(Subscription $subscription, User $user)
    public function __construct(Subscription $subscription, int $userId)
    {
        $this->subscription = $subscription;
//        $this->user = $user;
        $this->userId = $userId;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

//    /**
//     * @return User
//     */
//    public function getUser(): User
//    {
//        return $this->user;
//    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

}