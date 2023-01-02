<?php

namespace App\Websocket;

use App\Chat\Message;
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
        try {
            $message = json_decode($msg);
            $this->output->writeln('Received message; command=' . $message->command);

            $session = $this->chatManager->getSession($message->session);

            switch ($message->command) {
                case 'get_history':
                    $this->output->writeln('Found messages: ' . count($session->getChats()));
                    if (count($session->getChats())) {
                        $chats = $this->chatManager->getChats($session);
                        foreach ($chats as $chat) {
                            $message = new Message(
                                name: $chat->getName() . '1',
                                message: $chat->getMessage(),
                                session: $chat->getSession(),
                                isOperator: $chat->isIsOperator(),
                            );
                            $msg = json_encode($message);
                            $from->send($msg);
                        }
                    } else {
                        $answer = new Message(
                            name: 'Чат-бот',
                            message: 'Здравствуйте!',
                            session: $message->session,
                            isOperator: true,
                        );
                        $this->chatManager->addMessage($session, $answer);
                        $msg = json_encode($answer);
                        $from->send($msg);
                    }
                    break;
            }
        } catch (\Exception $exception) {
            $this->output->writeln($exception->getMessage());
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
