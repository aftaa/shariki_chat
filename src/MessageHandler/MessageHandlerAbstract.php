<?php

namespace App\MessageHandler;

use App\Manager\ChatManager;
use App\Manager\OperatorManager;

abstract class MessageHandlerAbstract
{
    public function __construct(
        protected OperatorManager $operatorManager,
        protected ChatManager $chatManager,
    )
    {
    }

    abstract public function handle(MessageHandlerDto $message): MessageHandlerDto;
}
