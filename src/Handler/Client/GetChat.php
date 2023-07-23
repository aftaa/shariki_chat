<?php

namespace App\Handler\Client;

use App\Entity\Chat;
use App\Handler\Handler as HandlerAlias;
use App\Message\ChatMessage;
use App\Message\Message;
use DateTime;
use stdClass;

class GetChat extends HandlerAlias
{
    /**
     * @throws \Exception
     */
    public function handle(Message $message): object
    {
        $session = $this->sessionService->get($message->getContent()->session);
        $chats = $this->chatService->getChats($session);

        $time = date('H:i');
        $autoChatOpen = $time >= '09:00' && $time < '21:00';

        if (count($chats)) {
            foreach ($chats as $chat) {
                $message = new ChatMessage(
                    $session->getName(),
                    $chat->getName(),
                    $chat->getMessage(),
                    $chat->isIsOperator(),
                    $this->dateService->format($chat->getCreated()),
                );
                $msg = json_encode($message);
                $this->sessionsConnections->send($session->getName(), $msg);
            }
        } elseif ($autoChatOpen) {
            $answer = new ChatMessage(
                session: $session->getName(),
                name: 'Чат-бот',
                message: $this->messageService->get('welcome'),
                isOperator: true,
                created: $this->dateService->format(new DateTime())
            );

            $chat = new Chat();
            $chat->setSession($session);
            $chat->setName($answer->name);
            $chat->setMessage($answer->message);
            $chat->setIsOperator($answer->isOperator);
            $chat->setCreated(new DateTime());
            $this->chatService->add($chat);

            $msg = json_encode($answer);
            $this->sessionsConnections->send($session->getName(), $msg);
        }

        return new stdClass();
    }
}
