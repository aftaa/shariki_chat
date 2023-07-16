<?php

namespace App\Handler;

use Ratchet\ConnectionInterface;

final class HandlerResponse
{
    public function send(ConnectionInterface $connection, Message $message): void
    {
        $response = json_encode($message);
        $connection->send($response);
    }
}
