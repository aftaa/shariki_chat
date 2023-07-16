<?php

namespace App\Handler;

use Ratchet\ConnectionInterface;

abstract class HandlerResponse
{
    public function send(ConnectionInterface $connection, MessageDto $message): void
    {
        $response = json_encode($message);
        $connection->send($response);
    }
}
