<?php

namespace App\Manager;

use App\Repository\ChatRepository;
use App\Repository\SessionRepository;

class OperatorManager
{
    public function __construct(
        public ChatRepository    $chatRepository,
        public SessionRepository $sessionRepository,
    )
    {
    }

    public function getSessions(): array
    {
        return $this->sessionRepository->getSessions();
    }
}