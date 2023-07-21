<?php

namespace App\Message;

class SessionMessage
{
    public function __construct(
        public int $id,
        public string $name,
        public string $started,
        public string $last_message,
        public int $message_count,
        public bool $has_new_message,
        public bool $hidden,
    )
    {
    }
}
