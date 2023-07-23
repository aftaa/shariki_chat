<?php

namespace App\Handler\Operator;

use App\Entity\Session;
use App\Handler\Handler;
use App\Message\Message;
use App\Message\SessionMessage;
use App\Message\SessionMessages;
use Exception;

class GetSessions extends Handler
{
    /**
     * @param Message $message
     * @return SessionMessages
     * @throws Exception
     */
    public function handle(Message $message): SessionMessages
    {
        $sessions = $this->sessionService->getSessions();
        $sessionMessages = new SessionMessages();
        foreach ($sessions as $session) {

            if (!empty($message->getContent()->skip) && $this->skip($session)) {
                continue;
            }

            $sessionMessage = new SessionMessage(
                $session['id'],
                $session['session'],
                $this->dateService->format($session['started']),
                $this->dateService->format($session['last_message']),
                $session['message_count'],
                (bool)$session['has_new_message'],
                (bool)!$session['has_new_message1'] && 1 == $session['message_count'],
            );
            $sessionMessages[] = $sessionMessage;
        }
        return $sessionMessages;
    }

    /**
     * @throws Exception
     */
    private function skip(array $session): bool
    {
        if ($session['timediff'] > 3600 * 24) {
            return true;
        }
        $format = $this->dateService->format($session['last_message']);
        if ('-' === $format) {
            return true;
        }
        if (!$session['has_new_message1'] && 1 == $session['message_count']) {
            return true;
        }
        return false;
    }
}
