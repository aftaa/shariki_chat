<?php

namespace App\Handler\Operator;

use App\Handler\AbstractHandler;
use App\Handler\MessageDto;

class GetHandlerTimeoutMessage extends AbstractHandler
{
    public function handle(MessageDto $message): MessageDto
    {
        $timeoutMessage = $this->chatManager->botTimeoutMessage();
        return new MessageDto('get_timeout_message', (object)[
            'timeout_message' => $timeoutMessage,
        ]);
    }
}
