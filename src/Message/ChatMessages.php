<?php

namespace App\Message;

class ChatMessages
{
    /**
     * @param ChatMessage[] $chats
     */
    public function __construct(
        public array $chats = [],
    )
    {
    }
}
