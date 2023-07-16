<?php

namespace App\Handler;

use App\Manager\ChatManager;
use App\Manager\OperatorManager;
use App\Service\WorkModeService;

abstract class AbstractHandler
{
    public function __construct(
        protected OperatorManager $operatorManager,
        protected ChatManager $chatManager,
        protected WorkModeService $workModeService,
    )
    {
    }

    abstract public function handle(MessageDto $message): MessageDto;
}
