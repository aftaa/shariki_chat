<?php

namespace App\Handler\Operator;

use App\Handler\Handler;
use App\Message\Message;

class SetTimeoutMessage extends Handler
{
    public function handle(Message $sessionMessage): object
    {
        $this->messageService->set('timeout', $sessionMessage->getContent()->timeout_message);
        return parent::handle($sessionMessage);
    }
}
