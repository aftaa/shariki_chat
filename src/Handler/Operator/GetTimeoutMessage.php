<?php

namespace App\Handler\Operator;

use App\Handler\Handler;
use App\Message\Message;

class GetTimeoutMessage extends Handler
{
    public function handle(Message $sessionMessage): object
    {
        $timeoutMessage = $this->messageService->get('timeout');
        return (object)[
            'timeout_message' => $timeoutMessage,
        ];
    }
}
