<?php

namespace App\MessageHandler;

abstract class MessageHandlerResponse
{
    public function sendResponse(MessageHandlerDto $message): void
    {
        $response = json_encode($message);
        $message->getConnection()->send($response);
    }
}
