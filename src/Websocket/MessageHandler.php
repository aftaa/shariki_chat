<?php

namespace App\Websocket;

use App\Handler\Handler;
use App\Handler\HandlerException;
use App\Handler\OperatorFactory;
use App\Message\Message;
use App\Message\SessionsMessage;
use App\Service\ChatService;
use App\Service\DateService;
use App\Service\OperatorManager;
use App\Service\PushSubService;
use App\Service\SessionService;
use Exception;
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
        private readonly ChatService        $chatManager,
        private readonly SessionService     $sessionService,
        private readonly MailerInterface    $mailer,
        private readonly PushSubService     $pushManager,
        private readonly Handler            $handler,
        private readonly ConnectionResponse $handlerResponse,
        private ConnectionManager           $operatorConnections = new ConnectionManager(),
        private SessionManager              $sessionsConnections = new SessionManager(),
        private DateService                 $chatDateManager = new DateService(),
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
            $this->output->writeln('[ OPERATOR ]');
            $this->operatorConnections->add($conn);
        }
        if (array_key_exists('session', $params)) {
            $this->output->writeln("[ SESSION $params[session] ]");
            $this->sessionsConnections->add($params['session'], $conn);
        }
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        try {
            $message = json_decode($msg);
            $message = new Message($message->command, $message);
            $this->output->writeln('');
            $this->output->writeln("[ {$message->getCommand()} ]");

            try {
                // обработка команды
                $messageContent = $this->handler->build($message->getCommand())->handle($message);

                // отправка ответа
                if ($messageContent instanceof SessionsMessage) {
                    $this->handlerResponse->sendSessions($from, $messageContent);
                } else {
                    $this->handlerResponse->send($from, new Message(
                        $message->getCommand(),
                        $messageContent,
                    ));
                }

            } catch (HandlerException $messageHandlerFactoryException) {
                $this->output->writeln($messageHandlerFactoryException->getMessage());

                switch ($message->getCommand()) {
                    case 'add_op_message':
                        $this->operatorAddMessage($from, $msg);
                        break;
                    case 'get_sessions':
                        $this->operatorGetSessions($from);
                        break;
                    case 'get_history':
                        $this->getHistory($message, $from);
                        break;
                    case 'get_op_history':
                        $this->operatorGetHistory($message, $from);
                        break;
                    case 'add_message':
                        $this->addMessage($message, $from, $msg);
                        break;
                }
            }
        } catch (Exception $exception) {
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
     * @param Exception $e
     * @return void
     */
    public function onError(ConnectionInterface $conn, Exception $e): void
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
            $this->output->writeln("[ close operator ]");
        }
        $session = $this->sessionsConnections->del($conn);
        if (false !== $session) {
            $this->output->writeln("[ close session $session ]");
        }
    }
}
