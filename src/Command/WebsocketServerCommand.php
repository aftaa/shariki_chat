<?php

namespace App\Command;

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

#[AsCommand(
    name: 'app:websocket-server',
)]
class WebsocketServerCommand extends Command
{


    public function __construct(
        private readonly SessionRepository $sessionRepository,
        private readonly ChatRepository    $chatRepository,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $port = 3001;
        $output->writeln("Starting server on port " . $port);
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new MessageHandler(
                        $this->sessionRepository,
                        $this->chatRepository,
                    )
                )
            ),
            $port
        );
        $server->run();
        return Command::SUCCESS;
    }
}
