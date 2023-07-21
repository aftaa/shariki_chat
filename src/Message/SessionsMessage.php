<?php

namespace App\Message;

class SessionsMessage
{
    public function __construct(
        public array $sessions = [],
    )
    {
    }
}
