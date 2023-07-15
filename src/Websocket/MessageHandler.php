<?php

namespace App\Websocket;

use App\Manager\ChatDateManager;
use App\Manager\ChatManager;
use App\Manager\OperatorManager;
use App\Manager\WebPushManager;
use App\MessageHandler\MessageHandlerDto;
use App\MessageHandler\MessageHandlerFactory;
use App\MessageHandler\MessageHandlerFactoryException;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MessageHandler implements MessageComponentInterface
{
    use MessageHandlerTrait;

    private OutputInterface $output;

    /**
     * @var ConnectionInterface[]
     */
    private array $sessions = [];

    public function __construct(
        private readonly ChatManager           $chatManager,
        private readonly OperatorManager       $operatorManager,
        private readonly MailerInterface       $mailer,
        private readonly WebPushManager        $pushManager,
        private readonly MessageHandlerFactory $messageHandlerFactory,
        private readonly ConnectionManager     $operatorConnections = new ConnectionManager(),
        private readonly SessionManager        $sessionsConnections = new SessionManager(),
        private readonly ChatDateManager       $chatDateManager = new ChatDateManager(),
    )
    {
    }

    /**
     * @param OutputInterface $output
     * @return $this
     */
    public function setOutput(OutputInterface $output): static
    {
        $this->output = $output;
        return $this;
    }

    public function onOpen(ConnectionInterface $conn): void
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

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        try {
            $decodedMessage = json_decode($msg);
            $requestMessage = new MessageHandlerDto($decodedMessage->command, $decodedMessage, $from);

            $this->output->writeln('');
            $this->output->writeln("[ {$requestMessage->getCommand()} ]");

            try {
                $responseMessage = $this->messageHandlerFactory->create($requestMessage->getCommand())->handle($requestMessage);
                $this->messageHandlerFactory->create($responseMessage->getCommand())->sendResponse($responseMessage);
            } catch (MessageHandlerFactoryException) {
                switch ($requestMessage->getCommand()) {
//                    case 'get_work_mode':
//                        $this->getWorkMode($from);
//                        break;
                    case 'set_work_mode':
                        $this->setWorkMode($requestMessage, $from);
                        break;
                    case 'add_op_message':
                        $this->operatorAddMessage($from, $msg);
                        break;
                    case 'get_sessions':
                        $this->operatorGetSessions($from);
                        break;
                    case 'get_sessions_all':
                        $this->operatorGetSessionsAll($from);
                        break;
                    case 'get_history':
                        $this->getHistory($requestMessage, $from);
                        break;
                    case 'get_op_history':
                        $this->operatorGetHistory($requestMessage, $from);
                        break;
                    case 'add_message':
                        $this->addMessage($requestMessage, $from, $msg);
                        break;
//                    case 'load_welcome_message':
//                        $this->loadWelcomeMessage(); // returns command 'welcome_message'
//                        break;
                    case 'load_timeout_message':
                        $this->loadTimeoutMessage(); // returns command 'timeout_message'
                        break;
                    case 'save_welcome_message':
                        $this->saveWelcomeMessage($requestMessage); // returns command 'welcome_message'
                        break;
                    case 'save_timeout_message':
                        $this->saveTimeoutMessage($requestMessage); // returns command 'timeout_message'
                        break;
                    case 'ping':
                        $this->output->writeln('[ ping pong ]');
                        $this->operatorManager->ping();
                        break;
                }
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
