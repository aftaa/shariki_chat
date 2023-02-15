<?php

namespace App\Websocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class ConnectionManager
{
    /**
     * @var ConnectionInterface[]
     */
    private array $connections = [];

    /**
     * @param ConnectionInterface $newConn
     * @return bool
     */
    public function exists(ConnectionInterface $newConn): bool
    {
        return in_array($newConn, $this->connections, true);
    }
    /**
     * @param ConnectionInterface $connection
     * @return void
     */
    public function add(ConnectionInterface $connection): void
    {
        if (!$this->exists($connection)) {
            $this->connections[] = $connection;
        }
    }

    /**
     * @param ConnectionInterface $delConn
     * @return bool
     */
    public function del(ConnectionInterface $delConn): bool
    {
        $key = array_search($delConn, $this->connections, true);
        if (false !== $key) {
            unset($this->connections[$key]);
            return true;
        }
        return false;
    }

    /**
     * @param string $message
     * @return void
     */
    public function send(string $message): void
    {
        foreach ($this->connections as $connection) {
            $connection->send($message);
        }
    }
}
