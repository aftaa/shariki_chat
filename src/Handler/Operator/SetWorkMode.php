<?php

namespace App\Handler\Operator;

use App\Handler\OperatorHandler;
use App\Handler\Message;

class SetWorkMode extends OperatorHandler
{
    public function handle(Message $message): object
    {
        $this->workModeService->set($message->getContent()->work_mode);
        return parent::handle($message);
    }
}
