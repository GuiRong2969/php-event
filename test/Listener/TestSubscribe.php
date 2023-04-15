<?php

namespace Guirong\Event\Test\Listener;

use Guirong\Event\Test\Event\TestCEvent;
use Guirong\Event\Test\Event\TestDEvent;

class TestSubscribe
{
    public function subscribe($events)
    {
        $events->listen(
            TestCEvent::class,
            self::class . '@testCEvent'
        );
        $events->listen(
            TestDEvent::class,
            self::class . '@testDEvent'
        );
    }

    public function testCEvent($event)
    {
    }

    public function testDEvent($event)
    {
    }
}
