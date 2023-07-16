<?php

namespace App\MessageHandler;

use App\Manager\ChatManager;
use App\Manager\OperatorManager;
use App\Service\WorkModeService;

abstract class MessageHandlerAbstract
{
    public function __construct(
        protected OperatorManager $operatorManager,
        protected ChatManager $chatManager,
        protected WorkModeService $workModeService,
    )
    {
    }

    abstract public function handle(MessageHandlerDto $message): MessageHandlerDto;
}
