<?php

namespace App\Command;

use App\Websocket\MessageHandler;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'wss')]
class Wss extends Command
{
    public final const PORT = 3001;

    public function __construct(
        private readonly MessageHandler $messageHandler
    )
    {
        date_default_timezone_set('Europe/Moscow');
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
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
