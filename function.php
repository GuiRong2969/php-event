<?php

namespace Guirong\Event;

use Guirong\Event\Helper\EventHelper;

/**
 * Event triggered
 *
 * @param string $service
 * @param mixed $event
 * @param mixed $payload
 * @return array
 */
function trigger(string $service, $event, $payload = []): array
{
    return EventHelper::trigger($service, $event, $payload);
}

/**
 * Register an event listener
 *
 * @param string $event
 * @param mixed $listener
 * @return void
 */
function listen(string $event, $listener)
{
    return EventHelper::listen($event, $listener);
}
