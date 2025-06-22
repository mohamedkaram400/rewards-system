<?php

use App\Strategy\Creators\PaymobPaymentStrategy;
use App\Strategy\Interfaces\StrategyFactoryInteface;


class ConcreteStrategyFactory implements StrategyFactoryInteface
{
    public function InitStrategy($type)
    {
        return match($type) {
            'straip' => new PaymobPaymentStrategy(),
            // 'paypal' => new PaypalPaymentStrategie(),
            default => throw new InvalidArgumentException("Unsupported type"),
        };
    }

}