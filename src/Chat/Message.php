<?php

namespace App\Chat;

class Message
{
    public function __construct(
        public ?string $name = null,
        public ?string $message = null,
        public ?string $session = null,
        public ?string $command = null,
        public ?bool $isOperator = null,
    )
    {
    }
}
