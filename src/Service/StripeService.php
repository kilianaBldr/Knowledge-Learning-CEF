<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeService
{
    public function __construct(string $stripeSecretKey)
    {
        Stripe::setApiKey($stripeSecretKey);
    }
    public function createCheckoutSession(array $lineItems, string $successUrl, string $cancelUrl): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl
        ]);
    }
}