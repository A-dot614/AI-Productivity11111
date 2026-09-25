<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AIProvider;
use App\Services\AI\DataTransferObjects\AIResponse;
use App\Services\AI\DataTransferObjects\Prompt;
use App\Services\AI\Exceptions\AIException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Throwable;

class AIService
{
    public function __construct(
        protected readonly AIProviderFactory $factory,
        protected readonly AIHistoryService $history,
        protected readonly PromptBuilder $promptBuilder,
    ) {}

    /**
     * Generate a completion from a named prompt template.
     *
     * @param  array<string, mixed>  $params
     * @param  array<string, mixed>  $options
     */
    public function generate(
        string $feature,
        string $template,
        array $params = [],
        array $options = [],
        ?string $provider = null,
        ?int $userId = null,
    ): AIResponse {
        $prompt = $this->promptBuilder->build($template, $params);

        return $this->execute($feature, $prompt, $options, $provider, $userId);
    }

    /**
     * Generate a completion from a pre-built Prompt object.
     *
     * @param  array<string, mixed>  $options
     */
    public function execute(
        string $feature,
        Prompt $prompt,
        array $options = [],
        ?string $provider = null,
        ?int $userId = null,
    ): AIResponse {
        if ($cached = $this->fromCache($prompt)) {
            return $cached;
        }

        $aiProvider = $this->factory->make($provider);
        $response = $this->request($aiProvider, $feature, $prompt, $options, $userId);

        if (config('ai.cache.enabled')) {
            Cache::put($this->cacheKey($prompt), $response, config('ai.cache.ttl', 3600));
        }

        return $response;
    }

    /**
     * Resolve the configured provider instance.
     */
    public function provider(?string $provider = null): AIProvider
    {
        return $this->factory->make($provider);
    }

    protected function request(AIProvider $aiProvider, string $feature, Prompt $prompt, array $options, ?int $userId = null): AIResponse
    {
        $startedAt = now();

        try {
            $response = $aiProvider->chat($prompt->toMessages(), $options);

            if (config('ai.logging.enabled')) {
                $this->history->record(
                    userId: $userId ?? $this->currentUserId(),
                    feature: $feature,
                    prompt: $prompt->user,
                    response: $response,
                    meta: ['system' => $prompt->system],
                );
            }

            return $response;
        } catch (AIException $e) {
            if (config('ai.logging.enabled')) {
                $this->history->recordFailure(
                    userId: $userId ?? $this->currentUserId(),
                    feature: $feature,
                    prompt: $prompt->user,
                    error: $e->getMessage(),
                    provider: $aiProvider->name(),
                );
            }

            throw $e;
        }
    }

    protected function currentUserId(): int
    {
        return (int) (Auth::id() ?? 0);
    }

    protected function cacheKey(Prompt $prompt): string
    {
        return 'ai:'.md5($prompt->system.$prompt->user);
    }

    protected function fromCache(Prompt $prompt): ?AIResponse
    {
        if (! config('ai.cache.enabled')) {
            return null;
        }

        try {
            return Cache::get($this->cacheKey($prompt));
        } catch (Throwable) {
            return null;
        }
    }
}
