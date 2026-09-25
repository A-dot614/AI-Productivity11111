<?php

namespace App\Services\AI\Contracts;

use App\Services\AI\DataTransferObjects\AIResponse;

interface AIProvider
{
    /**
     * The provider identifier (e.g. "openai", "gemini").
     */
    public function name(): string;

    /**
     * Whether the provider has been configured with valid credentials.
     */
    public function isConfigured(): bool;

    /**
     * Send a chat completion request.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options
     */
    public function chat(array $messages, array $options = []): AIResponse;
}
