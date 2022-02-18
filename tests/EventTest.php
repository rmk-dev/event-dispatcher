<?php

namespace RmkTests\EventDispatcher;

use PHPUnit\Framework\TestCase;
use Rmk\EventDispatcher\Events\Event;
use Rmk\EventDispatcher\Events\NamedEventTrait;

class NamedEvent extends Event {
    use NamedEventTrait;
}

class EventTest extends TestCase
{
    public function testEventTrait()
    {
        $emitter = new \stdClass();
        $params = new \ArrayObject(['a' => 1, 'b' => 2, 'c' => 3]);
        $event = new NamedEvent();
        $event->setEventName('test_event');
        $this->assertSame($emitter, $event->setEmitter($emitter)->getEmitter());
        $this->assertSame($params, $event->setParams($params)->getParams());
        $event->setParam('d', 4);
        $this->assertEquals(4, $event->getParam('d'));
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
        $this->assertEquals('test_event', $event->getEventName());
    }
}
