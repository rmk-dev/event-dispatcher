<?php

namespace Rmk\Event\Test;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Rmk\Event\EventDispatcher;
use Rmk\Event\EventInterface;
use Rmk\Event\Traits\EventDispatcherAwareTrait;
use Rmk\Event\Traits\EventTrait;

class TestDispatchEvent implements EventInterface
{
    use EventTrait;
}

class NotEventTestClass {

}

class EventDispatcherAwareTraitTest extends TestCase
{

    protected $dispatcher;

    /**
     * @var EventDispatcherInterface
     */
    protected $aware;

    protected function setUp(): void
    {
        $this->aware = $this->getMockForTrait(EventDispatcherAwareTrait::class);
        $this->dispatcher = $this->createStub(EventDispatcherInterface::class);
    }

    public function testGettersSetters()
    {
        $this->assertSame($this->aware, $this->aware->setEventDispatcher($this->dispatcher));
        $this->assertSame($this->dispatcher, $this->aware->getEventDispatcher());
    }

    public function testDispatch()
    {
        $this->assertInstanceOf(TestDispatchEvent::class, $this->aware->dispatchEvent(TestDispatchEvent::class));
        $this->aware->setEventDispatcher($this->dispatcher);
        $this->assertInstanceOf(TestDispatchEvent::class, $this->aware->dispatchEvent(TestDispatchEvent::class));
    }

    public function testThrowDispatchException()
    {
        $this->aware->setEventDispatcher($this->dispatcher);
        $this->expectException(\InvalidArgumentException::class);
        $this->aware->dispatchEvent(NotEventTestClass::class);
    }
}