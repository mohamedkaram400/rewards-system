<?php

namespace App\Exceptions;

use Exception;

class NotFoundProductsException extends Exception
{
    public function __construct($message = "No products found.")
    {
        parent::__construct($message);
    }
}
