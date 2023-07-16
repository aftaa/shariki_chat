<?php

namespace App\Handler;

use App\Manager\ChatManager;
use App\Manager\OperatorManager;
use App\Message;
use App\Service\MessageService;
use App\Service\WorkModeService;
use App\Websocket\ConnectionResponse;
use stdClass;
use function Symfony\Component\String\u;

class OperatorHandler
{
    /**
     * @param OperatorManager $operatorManager
     * @param ChatManager $chatManager
     * @param WorkModeService $workModeService
     * @param MessageService $messageService
     */
    public function __construct(
        protected OperatorManager $operatorManager,
        protected ChatManager $chatManager,
        protected WorkModeService $workModeService,
        protected MessageService $messageService,
    )
    {
    }

    /**
     * @param string $className
     * @return OperatorHandler|ConnectionResponse
     * @throws FactoryException
     */
    public function new(string $className): OperatorHandler|ConnectionResponse
    {
        $className = u($className)->camel();
        $className = ucfirst($className);
        $className = 'App\\Handler\\Operator\\' . $className;
        if (!class_exists($className)) {
            throw new FactoryException("Class $className not exists");
        }
        return new $className(
            $this->operatorManager,
            $this->chatManager,
            $this->workModeService,
            $this->messageService,
        );
    }

    /**
     * @param Message $message
     * @return object
     */
    public function handle(Message $message): object
    {
        return new stdClass();
    }
}
