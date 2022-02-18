<?php

namespace Rmk\EventDispatcher\Exception;

use LogicException;

class ListenerAlreadyAddedException extends LogicException implements
    EventExceptionInterface
{

}
