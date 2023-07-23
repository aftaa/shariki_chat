<?php

namespace App\Handler\Operator;

use App\Entity\Chat;
use App\Handler\Handler;
use App\Message\ChatMessage;
use App\Message\Message;
use DateTime;
use Exception;

class AddMessage extends Handler
{
    /**
     * @throws Exception
     */
    public function handle(Message $message): object
    {
        $session = $this->sessionService->get($message->getContent()->session);
        $chat = new Chat();
        $chat->setSession($session);
        $chat->setName($message->getContent()->name);
        $chat->setMessage($message->getContent()->message);
        $chat->setIsOperator($message->getContent()->isOperator);
        $chat->setCreated(new DateTime());
        $this->chatService->add($chat);

        $chatMessage = new ChatMessage(
            $chat->getSession()->getName(),
            $chat->getName(),
            $chat->getMessage(),
            $chat->isIsOperator(),
            $this->dateService->format($chat->getCreated())
        );

        $message = new Message(
            'new_message',
            $chatMessage,
        );

        $messageEncoded = json_encode($message);

        $this->operatorConnections->send($messageEncoded);
        $this->sessionsConnections->send($session->getName(), $messageEncoded);

        $message = new Message(
            'open_chat',
            (object)[
                'session' => $session->getName(),
            ],
        );
        $messageEncoded = json_encode($message);
        $this->sessionsConnections->send($session->getName(), $messageEncoded);

        $data = $this->sessionService->getSessionData($session->getName());
        if (!empty($data)) {
            $message = new Message(
                'operator_update_session',
                (object)[
                    'session' => $session->getName(),
                    'name' => $session->getName(),
                    'last_message' => $this->dateService->format($data['last_message']),
                    'started' => $this->dateService->format($data['started']),
                    'message_count' => $data['message_count'],
                    'has_new_message' => $data['has_new_message'],
                    'has_new_message1' => $data['has_new_message1'],
                    'hidden' => !$data['has_new_message1'] && 1 == $data['message_count'],
                ],
            );
            $messageEncoded = json_encode($message);
            $this->operatorConnections->send($messageEncoded);
        }
        return (object)[];
    }
}