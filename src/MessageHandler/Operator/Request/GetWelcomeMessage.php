<?php

namespace App\MessageHandler\Operator\Request;

use App\MessageHandler\MessageHandlerAbstract;
use App\MessageHandler\MessageHandlerDto;

class GetWelcomeMessage extends MessageHandlerAbstract
{
    public function handle(MessageHandlerDto $message): MessageHandlerDto
    {
        $welcomeMessage = $this->chatManager->botWelcomeMessage();
        return new MessageHandlerDto('Operator_Response_GetWelcomeMessage', (object)[
            'welcome_message' => $welcomeMessage,
        ], $message->getConnection());
    }
}
