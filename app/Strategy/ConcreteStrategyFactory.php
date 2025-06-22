<?php
namespace App\Strategy;

use App\Strategy\Creators\PaymobPaymentStrategy;
use App\Strategy\Interfaces\StrategyFactoryInteface;
use Illuminate\Testing\Exceptions\InvalidArgumentException;


class ConcreteStrategyFactory implements StrategyFactoryInteface
{
    public function InitStrategy($type)
    {
        return match($type) {
            'paymob' => new PaymobPaymentStrategy(),
            // 'paypal' => new PaypalPaymentStrategie(),
            default => throw new InvalidArgumentException("Unsupported type"),
        };
    }

}