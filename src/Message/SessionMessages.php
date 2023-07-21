<?php

namespace App\Message;

use App\Entity\Session;

class SessionMessages
{
    /**
     * @param SessionMessage[] $sessions
     */
    public function __construct(
        public array $sessions = [],
    )
    {
    }
}
