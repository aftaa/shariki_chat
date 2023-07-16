<?php

namespace App\Handler\Operator;

use App\Handler\AbstractHandler;
use App\Handler\MessageDto;

class GetHandlerWelcomeMessage extends AbstractHandler
{
    public function handle(MessageDto $message): MessageDto
    {
        $welcomeMessage = $this->chatManager->botWelcomeMessage();
        return new MessageDto('get_welcome_message', (object)[
            'welcome_message' => $welcomeMessage,
        ]);
    }
}
