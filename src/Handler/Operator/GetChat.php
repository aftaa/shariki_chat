<?php

namespace App\Handler\Operator;

use App\Handler\Handler;
use App\Message\ChatMessage;
use App\Message\ChatMessages;
use App\Message\Message;
use Exception;

class GetChat extends Handler
{
    /**
     * @throws Exception
     */
    public function handle(Message $message): ChatMessages
    {
        $session = $this->sessionService->get($message->content->session);
        $chats = $this->chatService->getChats($session);
        $chatMessages = new ChatMessages();
        foreach ($chats as $chat) {
            $chatMessage = new ChatMessage(
                name: $chat->getName(),
                message: $chat->getMessage(),
                session: (string)$chat->getSession(),
                isOperator: $chat->isIsOperator(),
                created: $this->dateService->format($chat->getCreated()),
            );
            $chatMessages[] = $chatMessage;
        }
        return $chatMessages;
    }
}