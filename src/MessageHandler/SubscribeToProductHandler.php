<?php


namespace App\MessageHandler;


use App\Entity\Subscription;
use App\Entity\User;
use App\Message\SubscribeToProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SubscribeToProductHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(SubscribeToProduct $subscribeToProduct)
    {
        $subscription = $subscribeToProduct->getSubscription();
        $user = $this->em->getRepository(User::class)->find($subscribeToProduct->getUserId());
        $email = $subscribeToProduct->getEmail();

        $new_subscription = new Subscription();
        $new_subscription->setStripeId($subscription->id);
        $new_subscription->setCurrentPeriodEndAt(new \DateTime(strtotime($subscription->current_period_end)));
        $new_subscription->setCurrentPeriodStartAt(new \DateTime(strtotime($subscription->current_period_start)));
        $new_subscription->setNickname($subscription->plan->nickname);
        $new_subscription->setRegularity($subscription->plan->interval);
        $new_subscription->setAmount(($subscription->plan->amount)/100);
        $new_subscription->setStatus('active');
        $new_subscription->setCustomer($user);
        $new_subscription->setCustomerEmail($email);
    
        $this->em->persist($new_subscription);
        $this->em->flush();

//        $normalizer = new ObjectNormalizer();
//        $encoder    = new JsonEncoder();
//        $serializer = new Serializer([$normalizer], [$encoder]);
//        $serializedNewSubscriptionObject = $serializer->serialize($new_subscription, 'json', ['ignored_attributes' => ['customer']]);
//        return $this->json($serializer->decode($serializedNewSubscriptionObject, 'json'));

    }
}