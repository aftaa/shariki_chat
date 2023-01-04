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
        private readonly SessionManager    $sessionsConnections = new SessionManager(),
    )
    {
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->output->writeln('New connection');
    }

    public function onMessage(ConnectionInterface $connection, $msg)
    {
        try {
            $message = json_decode($msg);
            $this->output->writeln('Received message; command=' . $message->command);

            switch ($message->command) {
                case 'add_op_message':
                    $this->operatorAddMessage($connection, $msg);
                    break;
                case 'get_sessions':
                    $this->operatorGetSessions($connection);
                    break;
                case 'get_history':
                    $this->getHistory($message, $connection);
                    break;
                case 'get_op_history':
                    $this->operatorGetHistory($message, $connection);
                    break;
                case 'add_message':
                    $this->addMessage($message, $connection, $msg);
            }
        } catch (\Exception $exception) {
            $this->output->writeln($exception->getMessage());
        }
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $msg
     * @return void
     * @throws \Exception
     */
    public function operatorAddMessage(ConnectionInterface $connection, string $msg): void
    {
        $this->operatorConnections->add($connection);
        $message = json_decode($msg);
        $session = $this->chatManager->getSession($message->session);
        $message->session = $session;
        $this->chatManager->addMessage($session, $message);

        $this->operatorConnections->send($msg);
        $this->sessionsConnections->send($session->getName(), $msg);
    }

    /**
     * @param ConnectionInterface $connection
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function operatorGetSessions(ConnectionInterface $connection): void
    {
        $this->operatorConnections->add($connection);

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
            $connection->send($msg);
        }
    }

    /**
     * @param mixed $message
     * @param ConnectionInterface $connection
     * @return void
     * @throws \Exception
     */
    public function getHistory(mixed $message, ConnectionInterface $connection): void
    {
        $this->sessionsConnections->add($message->session, $connection);
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
                $this->sessionsConnections->send($message->session, $msg);
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
            $this->sessionsConnections->send($message->session, $msg);
        }
    }

    /**
     * @param mixed $message
     * @param ConnectionInterface $connection
     * @return void
     */
    public function operatorGetHistory(mixed $message, ConnectionInterface $connection): void
    {
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
                $connection->send($msg);
            }
        }
    }

    /**
     * @param mixed $message
     * @param ConnectionInterface $connection
     * @param string $msg
     * @return void
     * @throws \Exception
     */
    public function addMessage(mixed $message, ConnectionInterface $connection, string $msg): void
    {
        $session = $this->chatManager->getSession($message->session);
        $this->sessionsConnections->add($message->session, $connection);
        $message = json_decode($msg);
        $message->isOperator = false;
        $message->session = $session;
        $this->chatManager->addMessage($session, $message);
        $this->sessionsConnections->send($session->getName(), $msg);
        $this->operatorConnections->send($msg);
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onClose(ConnectionInterface $conn): void
    {
        $this->operatorConnections->del($conn);
        $this->sessionsConnections->del($conn);
        $this->output->writeln('Close connection');
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @return void
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        $this->operatorConnections->del($conn);
        $this->sessionsConnections->del($conn);
        $conn->close();
    }
}
