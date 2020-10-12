<?php

namespace Rmk\Event;

use Psr\EventDispatcher\EventDispatcherInterface;

interface EventDispatcherAwareInterface
{

    public function setEventDispatcher(EventDispatcherInterface $dispatcher);

    public function getEventDispatcher(): ?EventDispatcherInterface;
}
