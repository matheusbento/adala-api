<?php

namespace App\Exceptions;

use Exception;

class InvalidStatusReversal extends Exception
{
    protected $message = 'Cannot revert initial status.';
}
