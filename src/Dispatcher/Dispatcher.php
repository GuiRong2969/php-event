<?php

namespace Guirong\Event\Dispatcher;

use Guirong\Event\Container\Container;
use Closure;

class Dispatcher
{
    /**
     * The registered event listeners.
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  string  $events
     * @param  mixed  $listener
     * @return void
     */
    public function listen(string $event, $listener)
    {
        $type = $this->getListenerType($listener);
        $this->listeners[$event][$type][] = $this->makeListener($listener);
    }

    /**
     * Register an event subscriber with the dispatcher.
     *
     * @param  object|string  $subscriber
     * @return void
     */
    public function subscribe($subscriber)
    {
        $subscriber = $this->resolveSubscriber($subscriber);

        $subscriber->subscribe($this);
    }

    /**
     * Resolve the subscriber instance.
     *
     * @param  object|string  $subscriber
     * @return mixed
     */
    protected function resolveSubscriber($subscribe): object
    {
        return Container::make($subscribe);
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  \Closure|string  $listener
     * @return Closure
     */
    protected function makeListener($listener):Closure
    {
        if (is_string($listener)) {
            return $this->createClassListener($listener);
        }
        return $this->createClourseListener($listener);
    }

    /**
     * Create the class based event callable.
     *
     * @param  string  $listener
     * @return Closure
     */
    protected function createClassListener(string $listener):Closure
    {
        return function ($payload) use ($listener) {
            [$class, $method] = $this->createClassWithMethod($listener);
            return Container::make($class)->{$method}(...$payload);
        };
    }

    /**
     * Create the class based event clourse callable.
     *
     * @param Closure $listener
     * @return Closure
     */
    protected function createClourseListener(Closure $listener):Closure
    {
        return function ($payload) use ($listener) {
            return $listener(...array_values($payload));
        };
    }

    /**
     * Parse the class listener into class and method.
     *
     * @param  string  $listener
     * @return array
     */
    protected function createClassWithMethod(string $listener): array
    {
        $listener = explode('@', $listener);
        $class = $listener[0];
        $method = !empty($listener[1]) ? $listener[1] : 'handle';
        return [$class, $method];
    }

    /**
     * Resolve the given payload and prepare them for dispatching.
     *
     * @param  mixed  $event
     * @param  mixed  $payload
     * @return array
     */
    protected function resolvePayload($event, $payload): array
    {
        if (is_object($event)) {
            $payload = $event;
        }
        $payload = is_null($payload) ? [] : (is_array($payload) ? $payload : [$payload]);
        return $payload;
    }

    /**
     * Fire an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @return array
     */
    public function dispatch($event, $payload = []): array
    {
        $payload = $this->resolvePayload($event, $payload);
        $responses = [];
        foreach ($this->getListeners($event) as $listener) {
            $response = $listener($payload);
            if ($response === false) {
                break;
            }
            $responses[] = $response;
        }
        return $responses;
    }

    /**
     * Get Event Listeners
     *
     * @param mixed] $event
     * @return array
     */
    public function getListeners($event): array
    {
        [$event, $type] = $this->getEventWithType($event);
        return $this->listeners[$event][$type] ?? [];
    }

    /**
     * Get event name and listening type
     *
     * @param mixed $event
     * @return array
     */
    protected function getEventWithType($event): array
    {
        [$event, $type] = [$this->getEventKey($event), $this->getListenerTypeByEvent($event)];
        return [$event, $type];
    }

    /**
     * Get the eventKey
     *
     * @param mixed $event
     * @return string
     */
    protected function getEventKey($event): string
    {
        if (is_object($event)) {
            return get_class($event);
        }
        return (string)$event;
    }

    /**
     * Get the type of listener by Event type
     *
     * @param mixed $listener
     * @return string
     */
    protected function getListenerTypeByEvent($event): string
    {
        return !is_object($event) ? 'clourseListener' : 'calssListener';
    }

    /**
     * Get the type of listener
     *
     * @param mixed $listener
     * @return string
     */
    protected function getListenerType($listener): string
    {
        return is_callable($listener) ? 'clourseListener' : 'calssListener';
    }
}
