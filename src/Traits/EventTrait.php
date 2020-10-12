<?php

namespace Rmk\Event\Traits;

use Psr\EventDispatcher\StoppableEventInterface;
use Rmk\Event\EventInterface;

/**
 * Trait EventTrait
 * @package Rmk\Event\Traits
 */
trait EventTrait
{

    /**
     * The calling code
     *
     * @var mixed
     */
    protected $emitter;
    /**
     * Flag to show whether the event is stopped
     *
     * @var bool
     */
    protected $stopped = false;
    /**
     * Additional optional parameters
     *
     * @var array
     */
    protected $params;

    /**
     * EventTrait constructor.
     *
     * @param mixed $emitter
     * @param array $params
     * @param bool  $stopped
     */
    public function __construct(
        $emitter = null,
        array $params = [],
        bool $stopped = false
    ) {
        $this->emitter = $emitter;
        $this->params = $params;
        $this->stopped = $stopped;
    }

    /**
     * Stops the event from further listener execution
     *
     * @param string $reason
     */
    public function stopPropagation(string $reason = ''): void
    {
        $this->stopped = true;
        $this->setParam(EventInterface::STOP_REASON, $reason);
        $parent = $this->getParentEvent();
        if ($parent && $parent instanceof StoppableEventInterface) {
            $parent->stopPropagation();
            if ($parent instanceof EventInterface) {
                $parent->setParam(EventInterface::STOP_REASON, $reason);
            }
        }
    }

    /**
     * Shows whether the event is stopped
     *
     * @return bool True if it is, otherwise false
     */
    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }

    /**
     * Returns the event's calling code if any
     *
     * @return mixed The calling code
     */
    public function getEmitter()
    {
        return $this->emitter;
    }

    /**
     * Sets the event's calling code
     *
     * @param mixed $emitter The calling code, usually object
     *
     * @return static Self-reference
     */
    public function setEmitter($emitter)
    {
        $this->emitter = $emitter;

        return $this;
    }

    /**
     * Returns the additional event parameters if any
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $name)
    {
        return $this->params[$name] ?? null;
    }

    public function setParam(string $name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Sets additional event parameters
     *
     * @param array $params The event parameters or null to reset
     *
     * @return static Self-reference
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventClass(): string
    {
        return get_class($this);
    }

    public function getParentEvent()
    {
        return $this->params[EventInterface::PARENT_EVENT] ?? null;
    }

    public function getStopReason(): string
    {
        return $this->getParam(EventInterface::STOP_REASON) . '';
    }
}
