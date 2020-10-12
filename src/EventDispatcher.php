<?php

namespace Rmk\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Class EventDispatcher
 * @package Rmk\Event
 */
class EventDispatcher implements EventDispatcherInterface
{

    /**
     * The event listeners provider
     *
     * @var ListenerProviderInterface
     */
    protected $listenerProvider;

    /**
     * EventDispatcher constructor.
     *
     * @param ListenerProviderInterface $listenerProvider
     */
    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->setListenerProvider($listenerProvider);
    }

    /**
     * Provide all relevant listeners with an event to process.
     *
     * @param object $event
     *   The object to process.
     *
     * @return object
     *   The Event that was passed, now modified by listeners.
     */
    public function dispatch(object $event)
    {
        $listeners = $this->listenerProvider->getListenersForEvent($event);
        foreach ($listeners as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
            $listener($event);
        }

        return $event;
    }

    /**
     * @return ListenerProviderInterface
     */
    public function getListenerProvider(): ListenerProviderInterface
    {
        return $this->listenerProvider;
    }

    /**
     * @param ListenerProviderInterface $listenerProvider
     *
     * @return EventDispatcher
     */
    public function setListenerProvider(
        ListenerProviderInterface $listenerProvider
    ): EventDispatcher {
        $this->listenerProvider = $listenerProvider;

        return $this;
    }
}
