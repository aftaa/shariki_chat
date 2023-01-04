<?php

namespace App\Websocket;

use App\Chat\Message;
use Ratchet\ConnectionInterface;

trait MessageHandlerTrait
{
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

        $message = json_decode($msg);
        $message->command = 'new_message';
        $msg = json_encode($message);
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
                'command' => 'session',
                'session' => (object)[
                    'name' => $session['session'],
                    'id' => $session['id'],
                    'last_message' => $session['last_message'],
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
                    command: 'new_message',
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
                    command: 'new_message',
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
        $message = json_decode($msg);
        $message->command = 'new_message';
        $msg = json_encode($message);
        $this->sessionsConnections->send($session->getName(), $msg);
        $this->operatorConnections->send($msg);
    }

    /**
     * @param ConnectionInterface $connection
     * @return void
     */
    private function getWorkMode(ConnectionInterface $connection): void
    {
        $this->output->writeln('Querying Work Mode');
        $workMode = $this->operatorManager->getWorkMode();
        $this->output->writeln('Work Mode is: ' . $workMode);
        $message = (object)[
            'command' => 'work_mode',
            'work_mode' => $workMode,
        ];
        $msg = json_encode($message);
        $connection->send($msg);
    }

    /**
     * @param mixed $message
     * @param ConnectionInterface $connection
     * @return void
     * @throws \Exception
     */
    private function setWorkMode(mixed $message, ConnectionInterface $connection): void
    {
        $this->operatorManager->setWorkMode($message->work_mode);
        $message = (object)[
            'command' => 'work_mode',
            'work_mode' => $message->work_mode,
        ];
        $msg = json_encode($message);
        $this->operatorConnections->send($msg);
        $this->sessionsConnections->sendAll($msg);
    }
}
