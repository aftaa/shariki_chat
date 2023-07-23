<?php

namespace App\Websocket;

use App\Message\ChatMessages;
use App\Message\Message;
use App\Message\SessionMessages;
use Ratchet\ConnectionInterface;

final class ConnectionResponse
{
    public function sendMessage(ConnectionInterface $connection, Message $message): void
    {
        $response = json_encode($message);
        $connection->send($response);
    }

    public function sendSessions(ConnectionInterface $connection, SessionMessages $sessionMessages): void
    {
        foreach ($sessionMessages as $sessionMessage) {
            $message = new Message(
                'operator_get_session',
                $sessionMessage,
            );
            $this->sendMessage($connection, $message);
        }
    }

    public function sendChats(ConnectionInterface $connection, ChatMessages $chatMessages): void
    {
        foreach ($chatMessages as $chatMessage) {
            $message = new Message(
                'operaror_get_chat',
                $chatMessage,
            );
            $this->sendMessage($connection, $message);
        }
    }
}
