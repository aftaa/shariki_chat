<?php

namespace App\Handler\Client;

use App\Handler\Handler;
use App\Message\Message;

class Ping extends Handler
{
    public function handle(Message $sessionMessage): object
    {
        echo "ping\n";
        echo $sessionMessage->getContent()->session, "\n\n";
        $this->workModeService->get('timeout');
        return parent::handle($sessionMessage);
    }
}