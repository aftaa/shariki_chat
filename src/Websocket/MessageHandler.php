<?php

namespace App\Websocket;

use App\Manager\ChatDateManager;
use App\Manager\ChatManager;
use App\Manager\OperatorManager;
use App\Manager\WebPushManager;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

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
        private readonly MailerInterface   $mailer,
        private readonly WebPushManager    $pushManager,
        private readonly ConnectionManager $operatorConnections = new ConnectionManager(),
        private readonly SessionManager    $sessionsConnections = new SessionManager(),
        private readonly ChatDateManager   $chatDateManager = new ChatDateManager(),
    )
    {
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $queryString = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryString, $params);
        if (array_key_exists('operator', $params)) {
            $this->output->writeln('New operator connection');
            $this->operatorConnections->add($conn);
        }
        if (array_key_exists('session', $params)) {
            $this->output->writeln('New session connection ' . $params['session']);
            $this->sessionsConnections->add($params['session'], $conn);
        }
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
                case 'get_sessions_all':
                    $this->operatorGetSessionsAll($connection);
                    break;
                case 'get_history':
                    $this->getHistory($message, $connection);
                    break;
                case 'get_op_history':
                    $this->operatorGetHistory($message, $connection);
                    break;
                case 'add_message':
                    $this->addMessage($message, $connection, $msg);
                    break;
                case 'load_welcome_message':
                    $this->loadWelcomeMessage(); // returns command 'welcome_message'
                    break;
                case 'load_timeout_message':
                    $this->loadTimeoutMessage(); // returns command 'timeout_message'
                    break;
                case 'save_welcome_message':
                    $this->saveWelcomeMessage($message); // returns command 'welcome_message'
                    break;
                case 'save_timeout_message':
                    $this->saveTimeoutMessage($message); // returns command 'timeout_message'
                    break;
                case 'ping':
                    $this->output->writeln('[ ping pong ]');
                    $this->operatorManager->ping();
                    break;
            }
        } catch (\Exception $exception) {
            $this->output->writeln(
                $exception->getMessage()
                . ' on line ' . $exception->getLine()
                . ' in file ' . $exception->getFile()
            );
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onClose(ConnectionInterface $conn): void
    {
        $this->closeConnections($conn);
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @return void
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        $this->closeConnections($conn);
        $conn->close();
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    private function closeConnections(ConnectionInterface $conn): void
    {
        if ($this->operatorConnections->del($conn)) {
            $this->output->writeln("Close operator connection");
        }
        $session = $this->sessionsConnections->del($conn);
        if (false !== $session) {
            $this->output->writeln("Close session connection $session");
        }
    }
}
