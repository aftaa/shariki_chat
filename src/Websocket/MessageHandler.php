<?php

namespace App\Websocket;

use App\Chat\Message;
use App\Manager\ChatManager;
use App\Manager\OperatorManager;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MessageHandler implements MessageComponentInterface
{
    /**
     * @var ConnectionInterface[]
     */
    private array $sessions = [];


    public function __construct(
        private readonly ChatManager       $chatManager,
        private readonly OperatorManager   $operatorManager,
        private readonly OutputInterface   $output,
        private readonly ConnectionManager $operatorConnections = new ConnectionManager(),
    )
    {
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->output->writeln('New connection');
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $message = json_decode($msg);
            $this->output->writeln('Received message; command=' . $message->command);

            switch ($message->command) {
                case 'add_op_message':
                    $this->operatorConnections->add($from);
                    $message = json_decode($msg);
                    $session = $this->chatManager->getSession($message->session);
                    $message->session = $session;
                    $this->chatManager->addMessage($session, $message);
                    $this->operatorConnections->send($msg);
                    if (array_key_exists($session->getName(), $this->sessions)) {
                        $this->sessions[$session->getName()]->send($msg);
                    }
                    break;
                case 'get_sessions':
                    $this->operatorConnections->add($from);

                    $sessions = $this->operatorManager->getSessions();
                    $this->output->writeln('OPERATOR Found sessions: ' . count($sessions));
                    foreach ($sessions as $session) {
                        $msg = (object)[
                            'type' => 'session',
                            'session' => (object)[
                                'name' => $session['session'],
                                'id' => $session['id'],
                                'started' => $session['last_message'],
                            ],
                        ];
                        $msg = json_encode($msg, JSON_FORCE_OBJECT);
                        $this->operatorConnections->send($msg);
                    }
                    break;
                case 'get_history':
                    $session = $this->chatManager->getSession($message->session);
                    $this->sessions[$session->getName()] = $from;
                    $chats = $this->chatManager->getChats($session);
                    $this->output->writeln('Found messages: ' . count($chats));
                    if (count($chats)) {
                        foreach ($chats as $chat) {
                            $message = new Message(
                                name: $chat->getName(),
                                message: $chat->getMessage(),
                                session: (string)$chat->getSession(),
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
                case 'get_op_history':
                    $session = $this->chatManager->getSession($message->session);
                    $chats = $this->chatManager->getChats($session);
                    $this->output->writeln('Found messages: ' . count($chats));
                    if (count($chats)) {
                        foreach ($chats as $chat) {
                            $message = new Message(
                                name: $chat->getName(),
                                message: $chat->getMessage(),
                                session: (string)$chat->getSession(),
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
                        $this->operatorConnections->send($msg);
                    }
                    break;
                case 'add_message':
                    $session = $this->chatManager->getSession($message->session);
                    $this->sessions[$session->getName()] = $from;

                    $message = json_decode($msg);
                    $message->isOperator = false;
                    $message->session = $session;
                    $this->chatManager->addMessage($session, $message);
                    $from->send($msg);
                    if (!is_null($this->operator)) {
                        if (!$this->operator->send($msg)) {
                            $this->operator = null;
                        }
                    }
            }
        } catch (\Exception $exception) {
            $this->output->writeln($exception->getMessage());
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->operatorConnections->del($conn);
        $this->output->writeln('Close connection');
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->operatorConnections->del($conn);
        $conn->close();
    }
}
