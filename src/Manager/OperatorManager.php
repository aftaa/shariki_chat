<?php

namespace App\Manager;

use App\Entity\Session;
use App\Entity\WorkMode;
use App\Enum\WorkModeEnum;
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
     * @return string
     */
    public function getWorkMode(): string
    {
        $workMode = $this->workModeRepository->find(1);
        if (!$workMode) {
            $workMode = new WorkMode();
            $workMode->setWorkMode(self::DEFAULT_WORK_MODE);
            $this->workModeRepository->save($workMode, true);
        }
        return $workMode->getWorkMode();
    }

    public function setWorkMode(string $newWorkMode): void
    {
        $workMode = $this->workModeRepository->find(1);
        $workMode->setWorkMode($newWorkMode);
        $this->workModeRepository->save($workMode, true);
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