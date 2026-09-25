<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProvider;
use App\Services\AI\DataTransferObjects\AIResponse;
use App\Services\AI\Exceptions\AIException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class OpenAIProvider implements AIProvider
{
    public function __construct(
        protected readonly array $config,
    ) {}

    public function name(): string
    {
        return 'openai';
    }

    public function isConfigured(): bool
    {
        return filled($this->config['api_key'] ?? null);
    }

    public function chat(array $messages, array $options = []): AIResponse
    {
        if (! $this->isConfigured()) {
            throw AIException::notConfigured($this->name());
        }

        $start = hrtime(true);

        try {
            $response = Http::withToken($this->config['api_key'])
                ->acceptJson()
                ->timeout($this->config['timeout'] ?? 60)
                ->post($this->config['base_url'].'/chat/completions', [
                    'model' => $options['model'] ?? $this->config['model'],
                    'messages' => $messages,
                    'temperature' => $options['temperature'] ?? 0.4,
                    'max_tokens' => $this->config['max_tokens'] ?? 1500,
                ]);
        } catch (ConnectionException $e) {
            throw AIException::requestFailed($this->name(), 'connection error: '.$e->getMessage());
        }

        if ($response->failed()) {
            throw AIException::requestFailed($this->name(), $response->json('error.message') ?? $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? null;
        $usage = $data['usage'] ?? [];

        if (blank($content)) {
            throw AIException::emptyResponse($this->name());
        }

        return new AIResponse(
            content: $content,
            provider: $this->name(),
            model: $options['model'] ?? $this->config['model'],
            tokensIn: $usage['prompt_tokens'] ?? null,
            tokensOut: $usage['completion_tokens'] ?? null,
            durationMs: (int) (hrtime(true) - $start) / 1_000_000,
            raw: $data,
        );
    }
}
