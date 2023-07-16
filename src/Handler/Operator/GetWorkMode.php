<?php

namespace App\Handler\Operator;

use App\Handler\OperatorHandler;
use App\Message;

class GetWorkMode extends OperatorHandler
{
    public function handle(Message $message): object
    {
        $workMode = $this->workModeService->get();
        return (object)[
            'work_mode' => $workMode,
        ];
    }
}
