<?php

namespace App\Service;

use App\Entity\Chat;
use App\Entity\Session;
use App\Message\ChatMessage;
use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use App\Repository\SessionRepository;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;

class ChatService
{
    public function __construct(
        public ChatRepository    $chatRepository,
        public SessionRepository $sessionRepository,
        public MessageRepository $messageRepository,
    )
    {
    }

    /**
     * @param string $name
     * @return array
     */
    public function get(string $name): array
    {
        $session = $this->sessionRepository->findSession($name);
        $isNewSession = false;
        if (!$session) {
            $isNewSession = true;
            $session = new Session();
            $session
                ->setName($name)
                ->setSessionStarted(new DateTime())
            ;
            $this->sessionRepository->save($session, true);
        }
        return [$session, $isNewSession];
    }

    /**
     * @param Session $session
     * @param ChatMessage|\stdClass $message
     * @return Chat
     */
    public function addMessage(Session $session, ChatMessage|\stdClass $message): Chat
    {
        $chat = new Chat();
        $chat
            ->setSession($session)
            ->setName($message->name)
            ->setMessage($message->message)
            ->setCreated(new DateTime())
            ->setIsOperator($message->isOperator);
        $this->chatRepository->save($chat, true);
        return $chat;
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

    /**
     * @param Session $session
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function isNewChat(Session $session): bool
    {
        return $this->chatRepository->isNewChat($session);
    }
}
