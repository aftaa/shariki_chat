<?php

namespace App\Handler\Operator;

use App\Handler\OperatorHandler;
use App\Message;

class SetTimeoutMessage extends OperatorHandler
{
    public function handle(Message $message): object
    {
        $this->messageService->set('timeout', $message->getContent()->timeout_message);
        return parent::handle($message);
    }
}
