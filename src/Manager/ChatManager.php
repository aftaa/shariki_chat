<?php

namespace App\Manager;

use App\Chat\Message;
use App\Entity\Chat;
use App\Entity\Session;
use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use App\Repository\SessionRepository;

class ChatManager
{
    public function __construct(
        public ChatRepository    $chatRepository,
        public SessionRepository $sessionRepository,
        public MessageRepository $messageRepository,
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

    /**
     * @param Session $session
     * @param Message|\stdClass $message
     * @return void
     */
    public function addMessage(Session $session, Message|\stdClass $message): void
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
        return $this->chatRepository->getChats($session);
    }

    /**
     * @return string
     */
    public function botWelcomeMessage(): string
    {
        return $this->messageRepository->find(1)->getText();
    }

    /**
     * @return string
     */
    public function botTimeoutMessage(): string
    {
        return $this->messageRepository->find(2)->getText();
    }

    /**
     * @param string $welcomeMessage
     * @return void
     */
    public function updateWelcomeMessage(string $welcomeMessage): void
    {
        $message = $this->messageRepository->find(1);
        $message->setText($welcomeMessage);
        $this->messageRepository->save($message, true);
    }

    /**
     * @param string $timeoutMessage
     * @return void
     */
    public function updateTimeoutMessage(string $timeoutMessage): void
    {
        $message = $this->messageRepository->find(2);
        $message->setText($timeoutMessage);
        $this->messageRepository->save($message, true);
    }
}
