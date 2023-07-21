<?php

namespace App\Message;

class ChatMessage
{
    public function __construct(
        public string $name,
        public string $message,
        public string $session,
        public bool $isOperator,
        public string $created,
    )
    {
    }
}
