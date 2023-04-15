<?php

namespace Guirong\Event\Test\Event;

class TestCEvent
{
    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
