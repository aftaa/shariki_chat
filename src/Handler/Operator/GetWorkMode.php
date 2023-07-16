<?php

namespace App\Handler\Operator;

use App\Handler\AbstractHandler;
use App\Handler\MessageDto;

class GetWorkMode extends AbstractHandler
{
    public function handle(MessageDto $message): MessageDto
    {
        $workMode = $this->workModeService->get();
        return new MessageDto('get_work_mode', (object)[
            'work_mode' => $workMode,
        ]);
    }
}
