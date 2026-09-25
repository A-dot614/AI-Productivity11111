<?php

namespace App\Services\AI\Exceptions;

use RuntimeException;

class AIException extends RuntimeException
{
    public static function notConfigured(string $provider): self
    {
        return new self("The AI provider \"{$provider}\" is not configured.");
    }

    public static function requestFailed(string $provider, string $message): self
    {
        return new self("The AI provider \"{$provider}\" failed: {$message}");
    }

    public static function emptyResponse(string $provider): self
    {
        return new self("The AI provider \"{$provider}\" returned an empty response.");
    }
}
