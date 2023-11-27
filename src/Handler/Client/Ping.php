<?php

namespace App\Handler\Client;

use App\Handler\Handler;
use App\Message\Message;

class Ping extends Handler
{
    public function handle(Message $sessionMessage): object
    {
        $sessionName = $sessionMessage->getContent()->session;
        $session = $this->sessionService->setLastPing($sessionName);
        return parent::handle($sessionMessage);
    }
}