<?php

namespace App\Handler\Operator;

use App\Handler\Handler;
use App\Message\Message;

class GetWelcomeMessage extends Handler
{
    public function handle(Message $sessionMessage): object
    {
        $welcomeMessage = $this->messageService->get('welcome');
        return (object)[
            'welcome_message' => $welcomeMessage,
        ];
    }
}
