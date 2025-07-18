<?php

namespace App\Exceptions;

use Exception;

class InsufficientPointsException extends Exception
{
    public function __construct($message = "Not enough points to redeem this product.")
    {
        parent::__construct($message);
    }
}