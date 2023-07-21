<?php

namespace App\Handler\Client;

use App\Handler\Handler;
use App\Message\Message;

class Ping extends Handler
{
    public function handle(Message $sessionMessage): object
    {
        $this->workModeService->get('timeout');
        return parent::handle($sessionMessage);
    }
}