<?php

namespace App\Handler\Operator;

use App\Handler\Handler;
use App\Message\Message;

class SetWorkMode extends Handler
{
    public function handle(Message $sessionMessage): object
    {
        $this->workModeService->set($sessionMessage->getContent()->work_mode);
        return parent::handle($sessionMessage);
    }
}
