<?php

namespace App\MessageHandler;

use Ratchet\ConnectionInterface;

readonly class MessageHandlerDto
{
    public function __construct(
        public string $command,
        public object $content,
    )
    {
        unset($this->content->command);
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return object
     */
    public function getContent(): object
    {
        return $this->content;
    }
}
