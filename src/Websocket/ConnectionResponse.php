<?php

namespace App\Websocket;

use App\Message\Message;
use App\Message\SessionsMessage;
use Ratchet\ConnectionInterface;

final class ConnectionResponse
{
    public function send(ConnectionInterface $connection, Message $message): void
    {
        $response = json_encode($message);
        $connection->send($response);
    }

    public function sendSessions(ConnectionInterface $connection, SessionsMessage $sessionsMessage): void
    {
        foreach ($sessionsMessage->sessions as $session) {
            $message = new Message(
                'operator_get_session',
                $session,
            );
            $this->send($connection, $message);
        }
    }
}
