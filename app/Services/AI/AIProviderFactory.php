<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AIProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\OpenAIProvider;
use App\Services\Setting\SettingService;
use InvalidArgumentException;

class AIProviderFactory
{
    public function __construct(
        protected readonly SettingService $settings,
    ) {}

    public function make(?string $provider = null): AIProvider
    {
        $provider ??= (string) $this->settings->get('ai_provider', config('ai.default_provider'));

        return match ($provider) {
            'openai' => new OpenAIProvider($this->resolveConfig('openai')),
            'gemini' => new GeminiProvider($this->resolveConfig('gemini')),
            default => throw new InvalidArgumentException("Unsupported AI provider [{$provider}]."),
        };
    }

    /**
     * Resolve provider configuration from the database settings, falling back
     * to the values defined in config/ai.php.
     */
    protected function resolveConfig(string $name): array
    {
        $defaults = config("ai.providers.{$name}", []);

        return [
            'base_url' => $this->settings->get('ai_base_url', $defaults['base_url'] ?? null),
            'api_key' => $this->settings->get('ai_api_key', $defaults['api_key'] ?? null),
            'model' => $this->settings->get('ai_model', $defaults['model'] ?? null),
            'max_tokens' => (int) $this->settings->get('ai_max_tokens', $defaults['max_tokens'] ?? 1500),
            'timeout' => (int) $this->settings->get('ai_timeout', $defaults['timeout'] ?? 60),
        ];
    }
}
