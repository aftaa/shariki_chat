<?php

namespace App\Websocket;

use App\Manager\ChatManager;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Console\Output\OutputInterface;

readonly class MessageHandler implements MessageComponentInterface
{
    public function __construct(
        private ChatManager       $chatManager,
        private OutputInterface   $output,
        private \SplObjectStorage $connections = new \SplObjectStorage(),
    )
    {
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->connections->attach($conn);
        $this->output->writeln('New connection');
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $message = json_decode($msg);
        $this->output->writeln('Received message; command=' . $message->command);

//        $session = $this->chatManager->getSession($message->session);

        switch ($message->command) {
            case 'get_history':
                $answer = (object)[
                    'name' => 'Оператор',
                    'message' => 'Здравствуйте!',
                    'session' => $message->session,
                ];

                $msg = json_encode($answer);
                $from->send($msg);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->output->writeln('Close connection');
        $this->connections->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->connections->detach($conn);
        $conn->close();
    }
}
