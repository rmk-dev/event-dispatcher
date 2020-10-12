<?php

namespace Rmk\Event;

use Closure;
use Ds\Map;
use Ds\PriorityQueue;
use Ds\Queue;
use Psr\EventDispatcher\ListenerProviderInterface;
use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;
use Rmk\Event\Exception\ClassNotExistsException;
use Rmk\Event\Exception\EventExceptionInterface;
use Rmk\Event\Exception\OutOfBoundsException;
use Rmk\Event\Exception\TypeMissingException;
use Rmk\CallbackResolver\CallbackResolver;
use Rmk\CallbackResolver\CallbackResolverAwareTrait;

class ListenerProvider implements ListenerProviderInterface
{
    use CallbackResolverAwareTrait;

    /**
     * All listeners
     *
     * @var Map
     */
    protected $listeners;

    /**
     * ListenerProvider constructor.
     *
     * @param CallbackResolver $resolver
     */
    public function __construct(CallbackResolver $resolver)
    {
        $this->listeners = new Map();
        $this->setCallbackResolver($resolver);
    }

    /**
     * Add listener to the listener provider
     *
     * The listener will be added to the event its parameter is instance of.
     * A priority can be added optionally, otherwise the default will be used.
     *
     * @param mixed $listener The event listener.
     * @param int   $priority [Optional] The listener priority. Default 0.
     *
     * @throws ReflectionException
     * @throws OutOfBoundsException
     * @throws TypeMissingException
     * @throws ClassNotExistsException
     */
    public function addListener($listener, int $priority = 0): void
    {
        $listener = $this->getCallbackResolver()->resolve($listener);
        $eventName = $this->getEventName($listener);
        $this->addEventListener($eventName, $listener, $priority);
    }

    public function addEventListener(string $event, $listener, int $priority = 0): void
    {
        if (!$this->listeners->hasKey($event)) {
            $eventListeners = new PriorityQueue();
            $this->listeners->put($event, $eventListeners);
        } else {
            /** @var PriorityQueue $eventListeners */
            $eventListeners = $this->listeners->get($event);
        }
        $eventListeners->push($listener, $priority);
    }

    /**
     * Extract event name a listener is for
     *
     * @param callable $listener The event listener
     *
     * @return string The event name
     *
     * @throws ReflectionException
     * @throws OutOfBoundsException
     * @throws TypeMissingException
     * @throws ClassNotExistsException
     */
    protected function getEventName(callable $listener): string
    {
        $reflection = new ReflectionFunction(Closure::fromCallable($listener));
        $params = $reflection->getParameters();
        if (count($params) !== 1) {
            throw new OutOfBoundsException(
                'Listener expects exactly one parameter!'
            );
        }
        /** @var ReflectionParameter $paramRef */
        $paramRef = $params[0];
        $paramType = $paramRef->getType();
        if (!$paramType) {
            throw new TypeMissingException(
                'The parameter must be type-hinted with the event class'
            );
        }

        $eventName = $paramType->getName();
        if (!class_exists($eventName) && !interface_exists($eventName)) {
            throw new ClassNotExistsException($eventName);
        }

        return $eventName;
    }

    /**
     * Check whether the provider contains a listener
     *
     * @param callable $listener
     *
     * @return bool
     *
     * @throws ReflectionException
     */
    public function hasListener(callable $listener): bool
    {
        try {
            $eventName = $this->getEventName($listener);
        } catch (EventExceptionInterface $e) {
            return false;
        }
        foreach ($this->listeners->get($eventName) as $listeners) {
            /** @var PriorityQueue $listener */
            if ($listeners === $listener) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns all listeners for an event object
     *
     * This method will return listeners for the event, all its parents and
     * all interfaces it implements.
     *
     * @param object $event
     *   An event for which to return the relevant listeners.
     *
     * @return iterable[callable]
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent(object $event): iterable
    {
        $class = get_class($event);
        $parents = class_parents($class);
        $interfaces = class_implements($class);

        $listeners = new Queue();
        if ($this->listeners->hasKey($class)) {
            $listeners->push(...$this->convertToCallable($this->listeners->get($class)));
        }

        foreach ($parents as $parent) {
            if ($this->listeners->hasKey($parent)) {
                $listeners->push(...$this->convertToCallable($this->listeners->get($parent)));
            }
        }

        foreach ($interfaces as $inter) {
            if ($this->listeners->hasKey($inter)) {
                $listeners->push(...$this->convertToCallable($this->listeners->get($inter)));
            }
        }

        return $listeners;
    }

    protected function convertToCallable(PriorityQueue $listeners): array
    {
        $callables = [];
        foreach ($listeners->toArray() as $listener) {
            $callables[] = $this->getCallbackResolver()->resolve($listener);
        }
        return $callables;
    }
}
