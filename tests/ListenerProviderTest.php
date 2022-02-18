<?php

namespace RmkTests\EventDispatcher;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Rmk\CallbackResolver\CallbackResolver;
use Rmk\Collections\Collection;
use Rmk\EventDispatcher\ListenerProvider;

class ListenerProviderTest extends TestCase
{

    public function testAddAndResolve(): void
    {
        $callbacks = [
            'test_callback' => function($event) {
                return new \stdClass();
            }
        ];
        $container = $this->createStub(ContainerInterface::class);
        $container->method('has')->willReturnCallback(static function ($arg1) use ($callbacks) {
            return array_key_exists($arg1, $callbacks);
        });
        $container->method('get')->willReturnCallback(static function ($arg1) use ($callbacks) {
            return $callbacks[$arg1];
        });

        $provider = new ListenerProvider(new CallbackResolver($container));
        $provider->addEventListener(\stdClass::class, 'test_callback');
        $listeners = $provider->getListenersForEvent(new \stdClass());
        $this->assertInstanceOf(Collection::class, $listeners);
        $this->assertEquals(1, $listeners->count());
        $this->assertEquals($callbacks['test_callback'], $listeners->get(0));
    }
}
