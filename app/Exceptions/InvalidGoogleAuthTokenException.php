<?php

namespace App\Exceptions;

use Exception;

class InvalidGoogleAuthTokenException extends Exception
{
    public function __construct()
    {
        $this->message = "Invalid Google Token";
    }
}
