<?php

namespace App\Websocket;

use App\Message;
use Ratchet\ConnectionInterface;

final class ConnectionResponse
{
    public function send(ConnectionInterface $connection, Message $message): void
    {
        $response = json_encode($message);
        $connection->send($response);
    }
}
