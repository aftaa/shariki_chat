<?php

namespace App\Websocket;

use App\Entity\Chat;
use App\Entity\Session;
use App\Repository\ChatRepository;
use App\Repository\SessionRepository;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

readonly class MessageHandler implements MessageComponentInterface
{
    public function __construct(
        private \SplObjectStorage $connections = new \SplObjectStorage(),
        private SessionRepository $sessionRepository,
        private ChatRepository $chatRepository,
    )
    {
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->connections->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
//        foreach ($this->connections as $connection) {
//            if ($connection === $from) {
//                continue;
//            }
//            $connection->send($msg);
//        }
        $message = json_decode($msg);
        $sessionKey = $message['session'];

        $session = $this->sessionRepository->findBy(['key' => $sessionKey]);
        if (!$session) {
            $session = new Session();
            $session->setKey($sessionKey);
            $session->setSessionStarted(new \DateTime());
            $this->sessionRepository->save($session, true);
        }

        switch ($message['command']) {
            case 'get_history':
                $chats = $session->getChats();

                if (!$chats) {
                    $welcomeChat = new Chat();
                    $welcomeChat->setName('Оператор');
                    $welcomeChat->setMessage('Добрый день!');
                    $welcomeChat->setIsOperator(true);
                    $welcomeChat->setCreated(new \DateTime());
                    $welcomeChat->setSession($session);
                    $session->addChat($welcomeChat);
                    $this->sessionRepository->save($session);

                    $chat = [
                        'name' => $welcomeChat->getName(),
                        'session' => $welcomeChat->getSession()->getKey(),
                        'message' => $welcomeChat->getMessage(),
                    ];

                    $chat = json_encode($chat);
                    $from->send($chat);

                } else {

                }

                break;
            case 'add_message':
                $from->send($msg);
                break;
            default:

        }

    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        $this->connections->detach($conn);
        $conn->close();
    }
}
