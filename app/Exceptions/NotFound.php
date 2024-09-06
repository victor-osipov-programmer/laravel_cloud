<?php

namespace App\Exceptions;

use Exception;

class NotFound extends GeneralError
{
    function __construct($message = 'Not found', $status = 404)
    {
        parent::__construct($message, $status);
    }
}
