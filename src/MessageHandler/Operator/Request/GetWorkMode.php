<?php

namespace App\MessageHandler\Operator\Request;

use App\MessageHandler\MessageHandlerAbstract;
use App\MessageHandler\MessageHandlerDto;

class GetWorkMode extends MessageHandlerAbstract
{
    public function handle(MessageHandlerDto $message): MessageHandlerDto
    {
        $workMode = $this->workModeService->get();
        return new MessageHandlerDto('Operator_Response_GetWorkMode', (object)[
            'work_mode' => $workMode,
        ]);
    }
}
