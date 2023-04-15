<?php

namespace Guirong\Event\Helper;

use Guirong\Event\Container\Container;
use Guirong\Event\Facade\Dispatcher;

class EventHelper
{
    /**
     * Event triggered
     *
     * @param string $service
     * @param mixed $event
     * @param mixed $payload
     * @return array
     */
    public static function trigger(string $service, $event, $payload = []): array
    {
        return Container::make($service)->dispatch($event, $payload);
    }

    /**
     * Register an event listener
     *
     * @param string $event
     * @param mixed $listener
     * @return void
     */
    public static function listen(string $event, $listener)
    {
        return Dispatcher::listen($event, $listener);
    }
}
