<?php

namespace Rmk\EventDispatcher\Events;

/**
 * Base event class
 */
class Event implements StoppableEventInterface
{
    use EventTrait;
    use StoppableEventTrait;
}
