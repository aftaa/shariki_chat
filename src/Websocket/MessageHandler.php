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
    use MessageHandlerTrait;
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
            $this->output->writeln('');
            $this->output->writeln("[ Command $message->command ]");

            switch ($message->command) {
                case 'get_work_mode':
                    $this->getWorkMode($connection);
                    break;
                case 'set_work_mode':
                    $this->setWorkMode($message, $connection);
                    break;
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
            $this->output->writeln(
                $exception->getMessage()
                . ' on line ' . $exception->getLine()
                . ' in file ' . $exception->getFile()
            );
        }
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
