<?php

namespace Guirong\Event;

use Guirong\Event\Facade\Dispatcher;
use BadMethodCallException;

class Event
{
    /**
     * Event listener
     *
     * @var array
     */
    protected $listen = [];

    /**
     * Event Subscribe
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * The registered event listeners.
     *
     * @var array
     */
    protected static $listeners = [];

    /**
     * The methods to dynamically pass through to the dispatcher.
     *
     * @var array
     */
    protected $passthru = [
        'listen', 'subscribe', 'dispatch'
    ];

    public function __construct()
    {
        $this->register();
    }

    /**
     * Global Event Registration
     */
    protected function register()
    {
        foreach ($this->listen as $event => $listeners) {
            foreach (array_unique((array)$listeners) as $listener) {
                Dispatcher::listen($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            Dispatcher::subscribe($subscriber);
        }
    }

    /**
     * Proxy Executor
     *
     * @param string $method
     * @param mixed $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->passthru)) {
            return Dispatcher::{$method}(...$args);
        }
        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.',
            static::class,
            $method
        ));
    }
}
