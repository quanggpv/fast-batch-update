<?php

namespace Quangpv\BatchUpdate\Exceptions;

use Exception;

class InvalidValuesLengthException extends Exception
{
    protected $message = 'Invalid values length';

    protected $code = 2;
}
