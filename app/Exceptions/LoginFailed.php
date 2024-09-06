<?php

namespace App\Exceptions;

use Exception;

class LoginFailed extends GeneralError
{
    function __construct($message = 'Login failed', $status = 403)
    {
        parent::__construct($message, $status);
    }
}
