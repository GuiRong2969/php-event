<?php

namespace Guirong\Event\Test\Event;

class TestAEvent
{
    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
