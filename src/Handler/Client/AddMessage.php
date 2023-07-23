<?php

namespace App\Handler\Client;

use App\Entity\Chat;
use App\Handler\Handler;
use App\Message\ChatMessage;
use App\Message\Message;
use DateTime;
use stdClass;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class AddMessage extends Handler
{
    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function handle(Message $message): object
    {
        $session = $this->sessionService->get($message->getContent()->session);

        $chat = new Chat();
        $chat->setSession($session);
        $chat->setName($message->getContent()->name);
        $chat->setMessage($message->getContent()->message);
        $chat->setIsOperator(false);
        $chat->setCreated(new DateTime());
        $this->chatService->add($chat);

        $message = new Message(
            'client_get_message',
            new ChatMessage(
                $session->getName(),
                $chat->getName(),
                $chat->getMessage(),
                $chat->isIsOperator(),
                $this->dateService->format($chat->getCreated()),
            ),
        );

        $msg = json_encode($message);
        $this->sessionsConnections->send($session->getName(), $msg);
        $this->operatorConnections->send($msg);

        $this->pushSubService->webPushSend($chat->getMessage());

        $email = (new Email())
            ->from(new Address($_ENV['EMAIL_FROM_ADDRESS'], $_ENV['EMAIL_FROM_NAME']))
            ->addTo($_ENV['EMAIL_TO'])
            ->bcc($_ENV['EMAIL_BCC'])
            ->subject('Чат: ' . $chat->getMessage())
            ->text($chat->getMessage());
        $this->mailer->send($email);

        if ('bot' === $this->workModeService->get()) {
            $chatMessage = new ChatMessage(
                $session->getName(),
                'Чат-бот',
                $this->messageService->get('timeout'),
                true,
                $this->dateService->format(new DateTime()),
            );
            $message = new Message('new_message', $chatMessage);
            $msg = json_encode($message);
            $this->operatorConnections->send($msg);
            $this->sessionsConnections->send($session->getName(), $msg);

            $chat = new Chat();
            $chat->setSession($session);
            $chat->setName($chatMessage->name);
            $chat->setMessage($chatMessage->message);
            $chat->setIsOperator(true);
            $chat->setCreated(new DateTime());
            $this->chatService->add($chat);
        }

        return new stdClass();
    }
}
