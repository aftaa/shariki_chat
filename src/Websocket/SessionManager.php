<?php

namespace App\Websocket;

use Ratchet\ConnectionInterface;

class SessionManager
{
    /**
     * @var ConnectionManager[]
     */
    private array $sessions = [];

    /**
     * @param string $session
     * @param ConnectionInterface $connection
     * @return void
     */
    public function add(string $session, ConnectionInterface $connection): void
    {
        if (!array_key_exists($session, $this->sessions)) {
            $this->sessions[$session] = new ConnectionManager();
        }
        $this->sessions[$session]->add($connection);
    }

    /**
     * @param ConnectionInterface $connection
     * @return void
     */
    public function del(ConnectionInterface $connection): void
    {
        foreach ($this->sessions as $session => $connectionManager) {
            $connectionManager->del($connection);
        }
    }

    /**
     * @param string $session
     * @param string $message
     * @return void
     * @throws \Exception
     */
    public function send(string $session, string $message): void
    {
        if (!array_key_exists($session, $this->sessions)) {
            throw new \Exception("Session '$session' not found");
        }
        $this->sessions[$session]->send($message);
    }
}