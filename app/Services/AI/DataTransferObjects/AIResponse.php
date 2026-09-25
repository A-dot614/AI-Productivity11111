<?php

namespace App\Services\AI\DataTransferObjects;

class AIResponse
{
    public function __construct(
        public readonly string $content,
        public readonly string $provider,
        public readonly ?string $model = null,
        public readonly ?int $tokensIn = null,
        public readonly ?int $tokensOut = null,
        public readonly ?int $durationMs = null,
        public readonly ?array $raw = null,
    ) {}

    /**
     * Attempt to decode the response content as JSON.
     */
    public function json(bool $associative = true): ?array
    {
        $decoded = json_decode($this->content, $associative);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }

    /**
     * Strip markdown code fences that providers sometimes wrap JSON in.
     */
    public function cleanedContent(): string
    {
        $content = trim($this->content);

        if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $content, $matches)) {
            return trim($matches[1]);
        }

        return $content;
    }
}
