<?php

namespace Guirong\Event\Test;

use Guirong\Event\Event;
use Guirong\Event\Helper\EventHelper;
use Guirong\Event\Test\Event\TestAEvent;
use Guirong\Event\Test\Event\TestBEvent;
use Guirong\Event\Test\Listener\TestSubscribe;
use Guirong\Event\Test\Listener\TestAListener;
use Guirong\Event\Test\Listener\TestBListener;

class EventService extends Event
{
    /**
     * Event listener
     *
     * @var array
     */
    protected $listen = [
        TestAEvent::class => TestAListener::class,
        TestBEvent::class => TestBListener::class,
    ];

    /**
     * Event Subscribe
     *
     * @var array
     */
    protected $subscribe = [
        TestSubscribe::class,
    ];

    /**
     * Global Event Registration
     */
    protected function register()
    {
        parent::register();

        // Additional Register simple event listeners
        EventHelper::listen('eEvent', function ($foo, $bar) {
            var_dump(['$foo' => $foo], ['$bar' => $bar]);
        });

        EventHelper::listen('fEvent', function ($foo, $bar) {
            var_dump(['$foo' => $foo], ['$bar' => $bar]);
        });
    }
}
