<?php

namespace App\MessageHandler;

use Ratchet\ConnectionInterface;

abstract class MessageHandlerResponse
{
    public function send(ConnectionInterface $connection, MessageHandlerDto $message): void
    {
        $response = json_encode($message);
        $connection->send($response);
    }
}
