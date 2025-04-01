<?php

namespace App\Exceptions;

use Exception;

class InvalidGoogleAuthException extends Exception
{
    public function __construct()
    {
        $this->message = "Invalid Google Auth Process";
    }
}
