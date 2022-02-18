<?php

namespace Rmk\EventDispatcher\Events;

use ArrayObject;

/**
 * EventInterface
 */
interface EventInterface
{

    /**
     * @return mixed
     */
    public function getEmitter();

    /**
     * @return ArrayObject
     */
    public function getParams(): ArrayObject;

    /**
     * @param ArrayObject $params
     *
     * @return $this
     */
    public function setParams(ArrayObject $params): self;

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getParam(string $name, $default = null);

    /**
     * @param string $name
     * @param $value
     *
     * @return void
     */
    public function setParam(string $name, $value): void;
}
