<?php

namespace App\Handler;

use App\Message\Message;
use App\Service\ChatService;
use App\Service\ConnectionService;
use App\Service\DateService;
use App\Service\MessageService;
use App\Service\PushSubService;
use App\Service\SessionConnectionService;
use App\Service\SessionService;
use App\Service\WorkModeService;
use stdClass;
use Symfony\Component\Mailer\MailerInterface;
use function Symfony\Component\String\u;

class Handler
{
    public function __construct(
        protected ChatService               $chatService,
        protected MessageService            $messageService,
        protected SessionService            $sessionService,
        protected WorkModeService           $workModeService,
        protected DateService               $dateService,
        protected MailerInterface           $mailer,
        protected PushSubService            $pushSubService,
        protected ?ConnectionService        $operatorConnections = null,
        protected ?SessionConnectionService $sessionsConnections = null,
    )
    {
    }

    /**
     * @throws HandlerException
     */
    public function build(string $className): Handler
    {
        $className = u($className)->camel();
        $className = ucfirst($className);
        $className = 'App\\Handler\\' . $className;

        if (str_contains($className, '\\Operator')) {
            $className = str_replace('\\Operator', '\\Operator\\', $className);
        }

        if (str_contains($className, '\\Client')) {
            $className = str_replace('\\Client', '\\Client\\', $className);
        }

        if (!class_exists($className)) {
            throw new HandlerException("Class $className not exists");
        }

        return new $className(
            $this->chatService,
            $this->messageService,
            $this->sessionService,
            $this->workModeService,
            $this->dateService,
            $this->mailer,
            $this->pushSubService,
        );
    }

    public function handle(Message $sessionMessage): object|array
    {
        return new stdClass();
    }

    public function setOperatorConnections(ConnectionService $operatorConnections): static
    {
        $this->operatorConnections = $operatorConnections;
        return $this;
    }

    public function setSessionsConnections(SessionConnectionService $sessionsConnections): static
    {
        $this->sessionsConnections = $sessionsConnections;
        return $this;
    }
}
