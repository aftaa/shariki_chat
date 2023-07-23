<?php

namespace App\Message;

use ArrayAccess;
use Iterator;

class ChatMessages implements ArrayAccess, Iterator
{
    /**
     * @param ChatMessage[] $chatMessages
     */
    public function __construct(
        private array $chatMessages = [],
        private int   $position = 0,
    )
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->chatMessages[$offset]);
    }

    public function offsetGet(mixed $offset): ?ChatMessage
    {
        return $this->chatMessages[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $sessionMessage): void
    {
        if (null === $offset) {
            $this->chatMessages[] = $sessionMessage;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->chatMessages[$offset]);
    }

    public function current(): ChatMessage
    {
        return $this->chatMessages[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->chatMessages[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
