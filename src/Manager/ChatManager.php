<?php

namespace App\Manager;

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
        $session = $this->sessionRepository->findBy(['key' => $sessionKey]);
        if (!$session) {
            $session = new Session();
            $session
                ->setKey($sessionKey)
                ->setSessionStarted(new \DateTime())
            ;
            $this->sessionRepository->save($session, true);
        }
        return $session;
    }
}
