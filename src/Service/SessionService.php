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
            $session->setLastMessage(null);
            $this->sessionRepository->save($session, true);
        }
        return $session;
    }
}
