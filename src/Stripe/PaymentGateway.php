<?php


namespace App\Stripe;

use Stripe\{Stripe, Subscription};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PaymentGateway
{
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    public function __construct(ParameterBagInterface $bag)
    {
        $this->bag = $bag;

        Stripe::setApiKey($this->bag->get('stripe_secret_key'));
    }

    public function cancel($subscriptionId)
    {
        $subscription = Subscription::retrieve($subscriptionId);

        $subscription->delete();
    }

    public function subscribe($data)
    {
//        \Stripe\Stripe::setApiKey('sk_test_Gw22NrsxU6aIlKApdYKsXgN700f1Ww1pAc');
//        $data = json_decode($request->getContent(), true);

        // This creates a new Customer and attaches the default PaymentMethod in one API call.
        $customer = \Stripe\Customer::create([
            'payment_method' => $data['payment_method'],
            'email' => $data['email'],
            'invoice_settings' => [
                'default_payment_method' => $data['payment_method']
            ]
        ]);

    }
}
