<?php


namespace Rmk\Event\Test;

use PHPUnit\Framework\TestCase as Unit;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Rmk\Event\EventDispatcher;

class EventDispatcherTest extends Unit

{

    public function testDispatch()
    {
        $listnerProvider = $this->getMockForAbstractClass(ListenerProviderInterface::class);
        $listnerProvider->method('getListenersForEvent')
            ->willReturnCallback(
                function ($event) {
                    return [
                        static function ($event) {
                            echo 1 . PHP_EOL;
                        },
                        static function ($event) {
                            echo 2 . PHP_EOL;
                        },
                        static function ($event) {
                            echo 3 . PHP_EOL;
                        },
                        static function ($event) {
                            echo 4 . PHP_EOL;
                        },
                        static function ($event) {
                            echo 5 . PHP_EOL;
                        },
                    ];
                }
            );
        $counter = 0;
        $event = $this->getMockForAbstractClass(StoppableEventInterface::class);
        $event->expects($this->exactly(3))
            ->method('isPropagationStopped')
            ->willReturnCallback(
                static function () use (&$counter) {
                    return $counter++ > 1;
                }
            );

        $dispatcher = new EventDispatcher($listnerProvider);
        $this->assertSame($listnerProvider, $dispatcher->getListenerProvider());
        $this->assertSame($event, $dispatcher->dispatch($event));
    }
}