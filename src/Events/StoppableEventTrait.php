<?php

namespace Rmk\EventDispatcher\Events;

trait StoppableEventTrait
{

    /**
     * @var bool
     */
    protected bool $isPropagationStopped = false;

    /**
     * @return void
     */
    public function stopPropagation(): void
    {
        $this->isPropagationStopped = true;
    }

    /**
     * @return bool
     */
    public function isPropagationStopped(): bool
    {
        return $this->isPropagationStopped;
    }
}