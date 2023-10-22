<?php

namespace App\Handler\Client;

use App\Handler\Handler;
use App\Message\Message;

class KeyPress extends Handler
{
    public function handle(Message $message): object
    {
        $session = $message->getContent()->session;
        $message = json_encode((object)[
            'command' => 'client_key_press',
            'session' => $session,
        ]);
        $this->operatorConnections->send($message);
        return (object)[];
    }
}
