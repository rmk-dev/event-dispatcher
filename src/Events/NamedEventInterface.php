<?php

namespace Rmk\EventDispatcher\Events;

/**
 * NamedEventInterface
 */
interface NamedEventInterface extends EventInterface
{

    public function getEventName(): string;
}