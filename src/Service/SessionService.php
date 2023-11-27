<?php

namespace App\Service;

use App\Entity\Session;
use App\Repository\SessionRepository;
use DateTime;
use Doctrine\DBAL\Exception;

class SessionService
{
    const DEFAULT_WORK_MODE = 'operator';

    public function __construct(
        private readonly SessionRepository $sessionRepository,
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

    public function get(string $name): ?Session
    {
        $session = $this->sessionRepository->findOneBy(['name' => $name]);
        if (!$session) {
            $session = new Session();
            $session->setName($name);
            $session->setSessionStarted(new DateTime());
            $session->setLastMessage(new DateTime());
            $this->sessionRepository->save($session, true);
        }
        return $session;
    }

    public function setLastPing(string $sessionName): void
    {
        $session = $this->sessionRepository->findOneBy(['name' => $sessionName]);
        if ($session) {
            $session->setLastPing(new \DateTime());
            $this->sessionRepository->save($sessionName, true);
        }
    }
}
