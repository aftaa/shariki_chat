<?php

namespace App\Handler\Operator;

use App\Handler\Handler;
use App\Message\Message;

class SetWelcomeMessage extends Handler
{
    public function handle(Message $sessionMessage): object
    {
        $this->messageService->set('welcome', $sessionMessage->getContent()->welcome_message);
        return parent::handle($sessionMessage);
    }
}
