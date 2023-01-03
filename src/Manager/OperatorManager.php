<?php

namespace App\Manager;

use App\Entity\Session;
use App\Repository\ChatRepository;
use App\Repository\SessionRepository;
use Doctrine\DBAL\Exception;

class OperatorManager
{
    public function __construct(
        public ChatRepository    $chatRepository,
        public SessionRepository $sessionRepository,
    )
    {
    }

    /**
     * @throws Exception
     * @return Session[]
     */
    public function getSessions(): array
    {
        return $this->sessionRepository->getSessions();
    }
}