<?php


namespace App\Stripe;

use Stripe\{PaymentIntent, Stripe, Subscription, Customer};
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
//        $subscription->delete();

        return $subscription;
    }

    public function subscribe($data)
    {
        // This creates a new Customer and attaches the default PaymentMethod in one API call.
        $customer = Customer::create([
            'payment_method' => $data['payment_method'],
            'email' => $data['email'],
            'invoice_settings' => [
                'default_payment_method' => $data['payment_method']
            ]
        ]);

        $subscription = Subscription::create([
            'customer' => $customer,
            'items' => [
                [
                    'plan' => $data['pricing_plan'],
                ],
            ],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        return $subscription;
    }

    public function pay()
    {
        $intent = PaymentIntent::create([
            'amount'   => 1055,
            'currency' => 'usd',
        ]);

        return $intent;
    }

}
