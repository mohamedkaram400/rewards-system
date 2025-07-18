<?php

namespace App\Exceptions;

use Exception;

class ProductDeletionException extends Exception
{
    public function __construct($message = "Product could not be deleted.", $code = 500)
    {
        parent::__construct($message, $code);
    }
}
