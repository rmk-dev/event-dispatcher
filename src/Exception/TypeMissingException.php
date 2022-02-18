<?php

namespace Rmk\EventDispatcher\Exception;

use LogicException;

class TypeMissingException extends LogicException implements
    EventExceptionInterface
{

}
