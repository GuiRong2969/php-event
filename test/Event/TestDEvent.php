<?php

namespace Guirong\Event\Test\Event;

class TestDEvent
{
    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
