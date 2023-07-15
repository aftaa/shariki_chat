<?php

namespace App\Command;

use App\Manager\ChatManager;
use App\Manager\OperatorManager;
use App\Manager\WebPushManager;
use App\Repository\ChatRepository;
use App\Repository\SessionRepository;
use App\Websocket\MessageHandler;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;

#[AsCommand(
    name: 'ws',
)]
class WebsocketServerCommand extends Command
{
    public final const PORT = 3001;

    public function __construct(
        private readonly MessageHandler $messageHandler
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        ini_set('display_errors', '1');
        date_default_timezone_set('Europe/Moscow');
        $output->writeln("Starting server on port " . self::PORT);
        $output->writeln((new \DateTime())->format('H:i:s'));
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    $this->messageHandler->setOutput($output),
                )
            ),
            self::PORT,
        );
        $server->run();
        return Command::SUCCESS;
    }
}
