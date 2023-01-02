<?php

namespace App\Manager;

use App\Chat\Message;
use App\Entity\Chat;
use App\Entity\Session;
use App\Repository\ChatRepository;
use App\Repository\SessionRepository;

class ChatManager
{
    public function __construct(
        public ChatRepository    $chatRepository,
        public SessionRepository $sessionRepository,
    )
    {
    }

    /**
     * @param string $sessionKey
     * @return Session
     */
    public function getSession(string $sessionKey): Session
    {
        $session = $this->sessionRepository->getSession($sessionKey);
        if (!$session) {
            $session = new Session();
            $session
                ->setName($sessionKey)
                ->setSessionStarted(new \DateTime())
            ;
            $this->sessionRepository->save($session, true);
        }
        return $session;
    }

    public function addMessage(Session $session, Message $message): void
    {
        $chat = new Chat();
        $chat
            ->setSession($session)
            ->setName($message->name)
            ->setMessage($message->message)
            ->setCreated(new \DateTime())
            ->setIsOperator($message->isOperator);
        $this->chatRepository->save($chat, true);
    }

    /**
     * @param Session $session
     * @return Chat[]
     */
    public function getChats(Session $session): array
    {
        $chats = $this->chatRepository->getChats($session);
        return $chats;
    }
}
