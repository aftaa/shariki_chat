<?php

namespace App\Service;

use App\Repository\ServerRepository;

class ServerService
{
    public function __construct(
        private ServerRepository $serverRepository,
    )
    {
    }

    public function get()
    {
        return $this->serverRepository->findOneBy(['active' => 1]);
    }
}