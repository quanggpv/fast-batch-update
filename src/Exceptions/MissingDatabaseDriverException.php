<?php

namespace Quangpv\BatchUpdate\Exceptions;

use Exception;

class MissingDatabaseDriverException extends Exception
{
    protected $message = 'Missing database driver: please check again';

    protected $code = 1;
}
