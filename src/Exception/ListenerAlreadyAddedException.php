<?php

namespace Rmk\Event\Exception;

use LogicException;

class ListenerAlreadyAddedException extends LogicException implements
    EventExceptionInterface
{

}
