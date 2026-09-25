<?php

namespace App\Services\AI\DataTransferObjects;

class Prompt
{
    public function __construct(
        public readonly string $system,
        public readonly string $user,
    ) {}

    public function toMessages(): array
    {
        $messages = [];

        if (filled($this->system)) {
            $messages[] = ['role' => 'system', 'content' => $this->system];
        }

        $messages[] = ['role' => 'user', 'content' => $this->user];

        return $messages;
    }
}
