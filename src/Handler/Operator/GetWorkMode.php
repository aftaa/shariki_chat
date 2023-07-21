<?php

namespace App\Handler\Operator;

use App\Handler\Handler;
use App\Message\Message;

class GetWorkMode extends Handler
{
    public function handle(Message $sessionMessage): object
    {
        $workMode = $this->workModeService->get();
        return (object)[
            'work_mode' => $workMode,
        ];
    }
}
