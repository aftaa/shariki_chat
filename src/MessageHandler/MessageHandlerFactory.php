<?php

namespace App\MessageHandler;

use App\Manager\ChatManager;
use App\Manager\OperatorManager;

readonly class MessageHandlerFactory
{
    public function __construct(
        private OperatorManager $operatorManager,
        private ChatManager $chatManager,
    )
    {
    }

    /**
     * @param string $className
     * @return MessageHandlerAbstract|MessageHandlerResponse
     * @throws MessageHandlerFactoryException
     */
    public function create(string $className): MessageHandlerAbstract|MessageHandlerResponse
    {
        $className = str_replace('_', '\\', $className);
        $className = 'App\\MessageHandler\\' . $className;
        if (!class_exists($className)) {
            throw new MessageHandlerFactoryException("Class $className not exists");
        }
        return new $className(
            $this->operatorManager,
            $this->chatManager,
        );
    }
}
