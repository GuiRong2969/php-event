<?php

namespace Guirong\Event\Test\Event;

class TestBEvent
{
    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
