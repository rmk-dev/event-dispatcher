<?php

namespace Rmk\EventDispatcher\Events;

/**
 * NamedEventTrait
 */
trait NamedEventTrait
{

    /**
     * @var string
     */
    protected string $eventName;

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @param string $eventName
     *
     * @return $this
     */
    public function setEventName(string $eventName): self
    {
        $this->eventName = $eventName;
        return $this;
    }
}