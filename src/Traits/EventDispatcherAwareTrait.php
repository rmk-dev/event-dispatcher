<?php

/**
 *
 */

namespace Rmk\Event\Traits;

use Psr\EventDispatcher\EventDispatcherInterface;
use Rmk\Event\EventInterface;

/**
 * Trait EventDispatcherAwareTrait
 * @package Rmk\Event\Traits
 */
trait EventDispatcherAwareTrait
{

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @return EventDispatcherInterface|null
     */
    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return self
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    public function dispatchEvent(
        string $eventName,
        array $params = []
    ): EventInterface {
        $event = new $eventName($this, $params);
        if (!$event instanceof EventInterface) {
            throw new \InvalidArgumentException('Invalid event class');
        }
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch($event);
        } else {
            $event->stopPropagation();
        }
        return $event;
    }
}
