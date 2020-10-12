<?php

namespace Rmk\Event\Test;

use PHPUnit\Framework\TestCase;
use Rmk\Event\EventInterface;
use Rmk\Event\Traits\EventTrait;

class TestEventClass implements EventInterface
{
    use EventTrait;
}

class EventTraitTest extends TestCase
{
    public function testEventTrait()
    {
        $emmiter = new \stdClass();
        $params = [1, 2, 3];
        $event = $this->getMockForTrait(EventTrait::class);
        $this->assertSame($emmiter, $event->setEmitter($emmiter)->getEmitter());
        $this->assertSame($params, $event->setParams($params)->getParams());
        $parent = new TestEventClass();
        $event->setParam(EventInterface::PARENT_EVENT, $parent);
        $event->stopPropagation('Just stop');
        $this->assertTrue($event->isPropagationStopped());
        $this->assertEquals('Just stop', $event->getStopReason());
        $this->assertEquals(get_class($event), $event->getEventClass());
    }

}