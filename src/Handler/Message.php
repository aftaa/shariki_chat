<?php

namespace App\Handler;

use Ratchet\ConnectionInterface;

class Message
{
    /**
     * @param string $command
     * @param object $content
     */
    public function __construct(
        public string $command,
        public object $content,
    )
    {
        unset($this->content->command);
        if (isset($this->content->content)) {
            $this->content = $this->content->content;
        }
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
