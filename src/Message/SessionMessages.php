<?php

namespace App\Message;

use App\Entity\Session;
use ArrayAccess;
use Iterator;

class SessionMessages implements ArrayAccess, Iterator
{
    /**
     * @param SessionMessage[] $sessionMessages
     */
    public function __construct(
        private array $sessionMessages = [],
        private int $position = 0,
    )
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->sessionMessages[$offset]);
    }

    public function offsetGet(mixed $offset): ?SessionMessage
    {
        return $this->sessionMessages[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $sessionMessage): void
    {
        if (null === $offset) {
            $this->sessionMessages[] = $sessionMessage;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->sessionMessages[$offset]);
    }

    public function current(): SessionMessage
    {
        return $this->sessionMessages[$this->position];
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
        return isset($this->sessionMessages[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
