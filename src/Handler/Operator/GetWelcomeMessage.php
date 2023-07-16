<?php

namespace App\Handler\Operator;

use App\Handler\OperatorHandler;
use App\Handler\Message;

class GetWelcomeMessage extends OperatorHandler
{
    public function handle(Message $message): object
    {
        $welcomeMessage = $this->messageService->get('welcome');
        return (object)[
            'welcome_message' => $welcomeMessage,
        ];
    }
}
