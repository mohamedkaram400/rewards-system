<?php
namespace App\Strategy\Interfaces;

interface PaymentMethodInterface
{
    public function excute($data);
}