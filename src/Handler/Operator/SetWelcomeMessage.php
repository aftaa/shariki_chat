<?php

namespace App\Handler\Operator;

use App\Handler\OperatorHandler;
use App\Message;

class SetWelcomeMessage extends OperatorHandler
{
    public function handle(Message $message): object
    {
        $this->messageService->set('welcome', $message->getContent()->welcome_message);
        return parent::handle($message);
    }
}
