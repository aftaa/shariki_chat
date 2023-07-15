<?php

namespace App\MessageHandler;

use Ratchet\ConnectionInterface;

readonly class MessageHandlerDto
{
    public function __construct(
        private string $command,
        private object $content,
        private ConnectionInterface $connection,
    )
    {
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

    /**
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}
