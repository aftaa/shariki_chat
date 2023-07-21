<?php

namespace App\Handler\Operator;

use App\Handler\Handler;
use App\Message\Message;
use App\Message\SessionMessage;
use App\Message\SessionsMessage;
use Exception;

class GetSessions extends Handler
{
    /**
     * @param Message $message
     * @return object|array
     * @throws Exception
     */
    public function handle(Message $message): object|array
    {
        $sessions = $this->sessionService->getSessions();
        $sessionsMessage = new SessionsMessage();
        foreach ($sessions as $session) {
            $sessionMessage = new SessionMessage(
                $session['id'],
                $session['session'],
                $this->dateService->format($session['started']),
                $this->dateService->format($session['last_message']),
                $session['message_count'],
                (bool)$session['has_new_message'],
                (bool)!$session['has_new_message1'] && 1 == $session['message_count'],
            );
            $sessionsMessage->sessions[] = $sessionMessage;
        }
        return $sessionsMessage;
    }
}
