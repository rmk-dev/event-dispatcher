# RMK Event Dispatcher

Simple PSR-14 event dispatcher.

## Examples

Simple attach and trigger an event listener:

```php
<?php

class MyEventClassName implements \Rmk\Event\EventInterface
{
    use \Rmk\Event\Traits\EventTrait;
}

/** @var Rmk\CallbackResolver\CallbackResolver $resolver */
$resolver = MyCallbackResolverFactory::create();

$listenerProvider = new Rmk\Event\ListenerProvider($resolver);

// Add event listener for MyEventClassName events (can add second parameter for integer priority):
$listenerProvider->addListener(function (MyEventClassName $event) {
    // do something with the event object
});

$eventDispatcher = new Rmk\Event\EventDispatcher($listenerProvider);
/*
Can use the following arguments for the event constructor:
  1. Emitter object - the object who emits the event
  2. Array with event parameters
  3. Boolean value if is stopped (by default false)
*/
$eventDispatcher->dispatch(new MyEventClassName());

```
