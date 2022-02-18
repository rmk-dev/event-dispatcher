<?php

namespace Rmk\EventDispatcher\Events;

use ArrayObject;

/**
 * Trait EventTrait
 *
 * @package Rmk\EventDispatcher\Events
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
     * Additional optional parameters
     *
     * @var ArrayObject
     */
    protected ArrayObject $params;

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
     * @param mixed $emitter
     *
     * @return self
     */
    public function setEmitter($emitter): self
    {
        $this->emitter = $emitter;

        return $this;
    }

    /**
     * @return ArrayObject
     */
    public function getParams(): ArrayObject
    {
        return $this->params;
    }

    /**
     * @param ArrayObject $params
     *
     * @return $this
     */
    public function setParams(ArrayObject $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getParam(string $name, $default = null)
    {
        return array_key_exists($name, $this->params) ? $this->params[$name] : $default;
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return void
     */
    public function setParam(string $name, $value): void
    {
        $this->params[$name] = $value;
    }
}
