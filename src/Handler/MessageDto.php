<?php

namespace App\Handler;

use Ratchet\ConnectionInterface;

readonly class MessageDto
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
