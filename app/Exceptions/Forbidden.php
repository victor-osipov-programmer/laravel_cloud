<?php

namespace App\Exceptions;

use Exception;

class Forbidden extends GeneralError
{
    function __construct($message = 'Forbidden for you', $status = 403)
    {
        parent::__construct($message, $status);
    }
}
