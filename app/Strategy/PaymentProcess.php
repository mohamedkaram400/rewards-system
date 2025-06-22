<?php
namespace App\Strategy;

use App\Strategy\Interfaces\PaymentMethodInterface;

class PaymentProcess
{
    public PaymentMethodInterface $strategy;
    public function setStrategy(PaymentMethodInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function paymentResponse()
    {
        return $this->strategy->excute();
    }
}
