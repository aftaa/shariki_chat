<?php

namespace App\Handler\Operator;

use App\Handler\OperatorHandler;
use App\Handler\Message;

class GetTimeoutMessage extends OperatorHandler
{
    public function handle(Message $message): object
    {
        $timeoutMessage = $this->messageService->get('timeout');
        return (object)[
            'timeout_message' => $timeoutMessage,
        ];
    }
}
