<?php

namespace App\Handler\Operator;

use App\Handler\Handler;
use App\Message\Message;

class KeyPress extends Handler
{
    /**
     * @throws \Exception
     */
    public function handle(Message $message): object
    {
        $session = $message->getContent()->session;
        $message = json_encode((object)[
            'command' => 'operator_key_press',
        ]);
        $this->sessionsConnections->send($session, $message);
        return (object)[];
    }
}