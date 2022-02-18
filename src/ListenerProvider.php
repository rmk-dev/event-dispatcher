<?php

namespace Rmk\EventDispatcher;

use Psr\EventDispatcher\ListenerProviderInterface;
use Rmk\Collections\Collection;
use Rmk\EventDispatcher\Events\NamedEventInterface;
use Rmk\CallbackResolver\CallbackResolver;
use Rmk\CallbackResolver\CallbackResolverAwareTrait;

/**
 * Provider of listener for specific event
 */
class ListenerProvider implements ListenerProviderInterface
{
    use CallbackResolverAwareTrait;

    /**
     * All listeners
     *
     * @var Collection
     */
    protected Collection $listeners;

    /**
     * ListenerProvider constructor.
     *
     * @param CallbackResolver $resolver
     */
    public function __construct(CallbackResolver $resolver)
    {
        $this->listeners = new Collection();
        $this->setCallbackResolver($resolver);
    }

    /**
     * Add a listener for event
     *
     * @param string $eventName
     * @param mixed $listener
     * @param int $priority
     *
     * @return ListenerProviderInterface
     */
    public function addEventListener(string $eventName, $listener, int $priority = 0): ListenerProviderInterface
    {
        $this->listeners
            ->getOrCreate($eventName, new Collection())
            ->getOrCreate($priority, new Collection())
            ->append($listener);

        return $this;
    }

    /**
     * @param object $event
     *   An event for which to return the relevant listeners.
     * @return iterable[callable]
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent(object $event): iterable
    {
        $original = $event instanceof NamedEventInterface ? $event->getEventName() : get_class($event);
        $eventNames = $this->getEventNamesChain($original);
        return $this->resolveListenersForEvent($eventNames);
    }

    /**
     * @param string|object $eventName
     * @return array
     */
    protected function getHierarchy($eventName): array
    {
        return array_merge(class_parents($eventName), class_implements($eventName));
    }

    /**
     * @param string $originalEvent
     * @return array|string[]
     */
    protected function getEventNamesChain(string $originalEvent): array
    {
        return (class_exists($originalEvent)) ?
            array_merge([$originalEvent], $this->getHierarchy($originalEvent)) :
            [$originalEvent];
    }

    /**
     * @param array $eventNames
     * @return Collection
     */
    protected function resolveListenersForEvent(array $eventNames): Collection
    {
        $resolved = new Collection();
        $sortFn = static function($a, $b) { return $b <=> $a; };
        foreach ($eventNames as $eventName) {
            /** @var Collection $collection */
            $collection = $this->listeners->getOrCreate($eventName, new Collection());
            $collection->uksort($sortFn);
            $collection->map(function ($priority) use ($resolved) {
                $priority->map(function ($listener) use ($resolved) {
                    $resolved->append($this->getCallbackResolver()->resolve($listener));
                });
            });
        }

        return $resolved;
    }
}
