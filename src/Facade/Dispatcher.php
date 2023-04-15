<?php

namespace Guirong\Event\Facade;

/**
 * @method static void listen($event,$listener)
 * @method static void subscribe($subscriber)
 * @method static mixed resolveSubscriber($subscribe)
 * @method static Closure makeListener($listener)
 * @method static Closure createClassListener($listener)
 * @method static Closure createClourseListener(Closure $listener)
 * @method static array createClassWithMethod($listener)
 * @method static array resolvePayload($event,$payload)
 * @method static array dispatch($event,$payload = array ())
 * @method static array getListeners(mixed] $event)
 * @method static array getEventWithType(mixed $event)
 * @method static string getEventKey(mixed $event)
 * @method static string getListenerTypeByEvent($event)
 * @method static string getListenerType(mixed $listener)
 *
 * @see \Guirong\Event\Dispatcher\Dispatcher
 */

use Guirong\Event\Facade\Facade;

class Dispatcher extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Guirong\Event\Dispatcher\Dispatcher::class;
    }
}
