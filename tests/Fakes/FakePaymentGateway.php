<?php

namespace Tests\Fakes;

use App\Billing\PaymentGateway;

class FakePaymentGateway implements PaymentGateway
{

    private $charges;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function charge($amount, $token)
    {
        $this->charges[] = $amount;
    }

    public function getValidTestToken()
    {
        return 'valid-token';
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }

}
