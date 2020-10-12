<?php


namespace Rmk\Event\Test;
use PHPUnit\Framework\TestCase as Unit;
use Psr\Container\ContainerInterface;
use Rmk\CallbackResolver\CallbackResolver;
use Rmk\Event\Exception\ClassNotExistsException;
use Rmk\Event\Exception\OutOfBoundsException;
use Rmk\Event\Exception\TypeMissingException;
use Rmk\Event\ListenerProvider;

interface TestInterface
{
}
class ParentEvent
{
}
class ExtendedEvent extends ParentEvent
{
}
class ThirdEvent extends ExtendedEvent
{
}
class FourthEvent extends ExtendedEvent implements TestInterface
{
}
class EventWithInterface implements TestInterface
{
}
class TestEvent
{
}

class ListenerProviderTest extends Unit
{

    protected $provider;

    protected $listeners;

    protected function getResolver()
    {
        $callbacks = [];
        $container = $this->createStub(ContainerInterface::class);
        $container->method('has')->willReturnCallback(static function ($arg1) use ($callbacks) {
            return array_key_exists($arg1, $callbacks);
        });
        $container->method('get')->willReturnCallback(static function ($arg1) use ($callbacks) {
            return $callbacks[$arg1];
        });
        return new CallbackResolver($container);
    }

    protected function setUp(): void
    {
        $this->provider = new ListenerProvider($this->getResolver());
        $this->listeners = [
            static function (ParentEvent $event) {
                // ...
            },
            static function (ExtendedEvent $event) {
                // ...
            },
            static function (ThirdEvent $event) {
                // ...
            },
            static function (EventWithInterface $event) {
                // ...
            },
            static function (TestEvent $event) {
                // ...
            },
            static function (FourthEvent $event) {
                // ...
            },
            static function (TestEvent $e) {
                // ...
            },
            static function (TestInterface $e) {
                // ...
            }
        ];
    }

    public function testAddListener()
    {
        foreach ($this->listeners as $listener) {
            $this->assertNull($this->provider->addListener($listener));
        }
    }

    public function testAddListenerWithoutType()
    {
        $this->expectException(TypeMissingException::class);
        $this->provider->addListener(static function ($param) {
            // ...
        });
    }

    public function testAddListenerWithMoreParams()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->provider->addListener(static function () {
            // ...
        });
    }

    public function testGetListenersForEvent()
    {
        foreach ($this->listeners as $listener) {
            $this->provider->addListener($listener);
        }
        $event1 = new ParentEvent();
        $event2 = new ExtendedEvent();
        $event3 = new ThirdEvent();
        $event4 = new FourthEvent();
        $event5 = new EventWithInterface();
        $event6 = new TestEvent();

        $listeners1 = $this->provider->getListenersForEvent($event1);
        $this->assertCount(1, $listeners1);
        foreach ($listeners1 as $listener) {
            $this->assertEquals($this->listeners[0], $listener);
        }

        $listeners2 = $this->provider->getListenersForEvent($event2);
        $this->assertCount(2, $listeners2);
        foreach ($listeners2 as $key => $listener) {
            $this->assertEquals($this->listeners[$key + 1], $listener);
        }

        $listeners3 = $this->provider->getListenersForEvent($event3);
        $this->assertCount(3, $listeners3);
        $allListeners3 = [
            $this->listeners[0], $this->listeners[1], $this->listeners[2],
        ];
        foreach ($listeners3 as $listener) {
            $this->assertContains($listener, $allListeners3);
        }

        $listeners4 = $this->provider->getListenersForEvent($event4);
        $this->assertCount(4, $listeners4);
        $allListeners4 = [
            $this->listeners[0],
            $this->listeners[1],
            $this->listeners[5],
            $this->listeners[7],
        ];
        foreach ($listeners4 as $key => $listener) {
            $this->assertContains($listener, $allListeners4);
        }
    }

    public function testHasListener()
    {
        foreach ($this->listeners as $listener) {
            $this->provider->addListener($listener);
        }
        foreach ($this->listeners as $listener) {
            $this->assertTrue($this->provider->hasListener($listener));
        }

        $invalid = static function ($e) {
        };
        $this->assertFalse($this->provider->hasListener($invalid));

        foreach ($this->listeners as $listener) {
            $this->provider->addListener($listener);
        }
        $callback = static function (FourthEvent $e) {
        };
        $this->assertFalse($this->provider->hasListener($callback));
    }

    public function testClassNotExistsException()
    {
        $invalid = static function (InvalidClass $e) {
        };
        $this->expectException(ClassNotExistsException::class);
        $this->provider->addListener($invalid);
    }
}
