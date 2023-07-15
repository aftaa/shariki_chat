<?php

namespace App\MessageHandler\Operator\Request;

use App\MessageHandler\MessageHandlerAbstract;
use App\MessageHandler\MessageHandlerDto;

class GetWorkMode extends MessageHandlerAbstract
{
    public function handle(MessageHandlerDto $message): MessageHandlerDto
    {
        $workMode = $this->operatorManager->getWorkMode();
        return new MessageHandlerDto('Operator_Response_WorkMode_Get', (object)[
            'work_mode' => $workMode,
        ], $message->getConnection());
    }
}
