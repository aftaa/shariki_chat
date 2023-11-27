<?php

namespace App\Message;

class ChatMessage
{
    public function __construct(
        public string $session,
        public string $name,
        public string $message,
        public bool $isOperator,
        public string $created,
    )
    {
    }
}
