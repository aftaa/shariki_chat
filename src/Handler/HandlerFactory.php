<?php

namespace App\Handler;

use App\Manager\ChatManager;
use App\Manager\OperatorManager;
use App\Service\WorkModeService;

readonly class HandlerFactory
{
    public function __construct(
        private OperatorManager $operatorManager,
        private ChatManager $chatManager,
        private WorkModeService $workModeService,
    )
    {
    }

    /**
     * @param string $className
     * @return AbstractHandler|HandlerResponse
     * @throws HandlerFactoryException
     */
    public function create(string $className): AbstractHandler|HandlerResponse
    {
        $className = str_replace('_', '\\', $className);
        $className = 'App\\MessageHandler\\Operator\\' . $className;
        if (!class_exists($className)) {
            throw new HandlerFactoryException("Class $className not exists");
        }
        return new $className(
            $this->operatorManager,
            $this->chatManager,
            $this->workModeService,
        );
    }
}
