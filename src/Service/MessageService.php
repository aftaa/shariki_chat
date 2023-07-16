<?php

namespace App\Service;

use App\Repository\MessageRepository;

class MessageService
{
    /**
     * @param MessageRepository $messageRepository
     */
    public function __construct(
        private readonly MessageRepository $messageRepository,
    )
    {
    }

    /**
     * @param string $name
     * @return string
     */
    public function get(string $name): string
    {
        return $this->messageRepository->findOneBy(['name' => $name])->getText();
    }

    /**
     * @param string $name
     * @param string $text
     * @return void
     */
    public function set(string $name, string $text): void
    {
        $message = $this->messageRepository->findOneBy(['name' => $name]);
        $message->setText($text);
        $this->messageRepository->save($message, true);
    }
}
