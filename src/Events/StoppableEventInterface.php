<?php

namespace Rmk\EventDispatcher\Events;

use Psr\EventDispatcher\StoppableEventInterface as PsrStoppableEventInterface;

/**
 * StoppableEventInterface
 */
interface StoppableEventInterface extends PsrStoppableEventInterface, EventInterface
{

    public function stopPropagation(): void;
}