<?php

namespace App\Exceptions;

use Exception;

class GeneralError extends Exception
{
    public $message;
    public $status;
    
    function __construct($message, $status)
    {
        $this->message = $message;
        $this->status = $status;
    }
}
