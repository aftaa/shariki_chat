<?php

namespace App\Manager;

use App\Entity\Session;
use App\Repository\ChatRepository;
use App\Repository\SessionRepository;
use App\Repository\WorkModeRepository;
use Doctrine\DBAL\Exception;

class OperatorManager
{
    const DEFAULT_WORK_MODE = 'operator';

    public function __construct(
        public ChatRepository     $chatRepository,
        public SessionRepository  $sessionRepository,
        public WorkModeRepository $workModeRepository,
    )
    {
    }

    /**
     * @return Session[]
     * @throws Exception
     */
    public function getSessions(): array
    {
        return $this->sessionRepository->getSessions();
    }

    /**
     * @throws Exception
     */
    public function getSessionData(string $sessionName): false|array
    {
        return $this->sessionRepository->getSessionData($sessionName);
    }

    public function ping(): void
    {
        $this->workModeRepository->find(1);
    }
}