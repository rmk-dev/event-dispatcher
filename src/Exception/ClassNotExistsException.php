<?php

namespace Rmk\EventDispatcher\Exception;

use LogicException;
use Throwable;

class ClassNotExistsException extends LogicException
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = 'Unknown event name: ' . $message;
        parent::__construct($message, $code, $previous);
    }
}
