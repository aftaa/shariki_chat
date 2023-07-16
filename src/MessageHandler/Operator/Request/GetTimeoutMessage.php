<?php

namespace App\MessageHandler\Operator\Request;

use App\MessageHandler\MessageHandlerAbstract;
use App\MessageHandler\MessageHandlerDto;

class GetTimeoutMessage extends MessageHandlerAbstract
{
    public function handle(MessageHandlerDto $message): MessageHandlerDto
    {
        $timeoutMessage = $this->chatManager->botTimeoutMessage();
        return new MessageHandlerDto('Operator_Response_GetTimeoutMessage', (object)[
            'timeout_message' => $timeoutMessage,
        ]);
    }
}
