<?php

use Guirong\Event\Helper\EventHelper;
use Guirong\Event\Test\Event\TestAEvent;
use Guirong\Event\Test\Event\TestBEvent;
use Guirong\Event\Test\Event\TestCEvent;
use Guirong\Event\Test\Event\TestDEvent;
use Guirong\Event\Test\EventService;

define('DS', DIRECTORY_SEPARATOR);

if (is_file(dirname(__DIR__, 3) . DS . 'autoload.php')) {
    require dirname(__DIR__, 3) . DS . 'autoload.php';
} elseif (is_file(dirname(__DIR__) . DS . 'vendor' . DS . 'autoload.php')) {
    require dirname(__DIR__) . DS . 'vendor' . DS . 'autoload.php';
}

// Test Trigger Event 'TestA', Listener is TestAListener
EventHelper::trigger(
    EventService::class,
    new TestAEvent(
        ['i’m TestAEvent‘s payload']
    )
);

// Test Trigger Event 'TestB', Listener is TestBListener
Guirong\Event\trigger(
    EventService::class,
    new TestBEvent(
        ['i’m TestBEvent‘s payload']
    )
);

// Test Trigger Event 'TestC', Listener is TestCListener
Guirong\Event\trigger(
    EventService::class,
    new TestCEvent(
        ['i’m TestCEvent‘s payload']
    )
);

// Test Trigger Event 'TestD', Listener is TestDListener
Guirong\Event\trigger(
    EventService::class,
    new TestDEvent(
        ['i’m TestDEvent‘s payload']
    )
);

// Test Trigger Additional Event 'eEvent', Listener is an closure function
Guirong\Event\trigger(
    EventService::class,
    'eEvent'
);

// Test Trigger Additional Event 'fEvent', Listener is an closure function
Guirong\Event\trigger(
    EventService::class,
    'fEvent'
);
