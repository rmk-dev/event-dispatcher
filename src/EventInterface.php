<?php

namespace Rmk\Event;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Interface EventInterface
 * @package Rmk\Event
 */
interface EventInterface extends StoppableEventInterface
{

    public const PARENT_EVENT = 'parent_event';

    public const STOP_REASON = 'stop_reason';

    public function stopPropagation(): void;

    public function getEmitter();

    public function getEventClass(): string;

    public function getStopReason(): string;

    public function getParentEvent();

    public function getParams(): array;

    public function setParams(array $params);

    public function getParam(string $name);

    public function setParam(string $name, $value);
}
