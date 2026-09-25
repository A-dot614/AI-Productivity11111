<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProvider;
use App\Services\AI\DataTransferObjects\AIResponse;
use App\Services\AI\Exceptions\AIException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements AIProvider
{
    public function __construct(
        protected readonly array $config,
    ) {}

    public function name(): string
    {
        return 'gemini';
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

        $model = $options['model'] ?? $this->config['model'];
        $start = hrtime(true);

        try {
            $response = Http::acceptJson()
                ->timeout($this->config['timeout'] ?? 60)
                ->withQueryParameters(['key' => $this->config['api_key']])
                ->post($this->config['base_url']."/models/{$model}:generateContent", [
                    'systemInstruction' => ['parts' => [['text' => $messages[0]['content'] ?? '']]],
                    'contents' => collect(array_slice($messages, 1))->map(fn ($m) => [
                        'role' => $m['role'] === 'assistant' ? 'model' : 'user',
                        'parts' => [['text' => $m['content']]],
                    ])->values()->all(),
                    'generationConfig' => [
                        'temperature' => $options['temperature'] ?? 0.4,
                        'maxOutputTokens' => $this->config['max_tokens'] ?? 1500,
                    ],
                ]);
        } catch (ConnectionException $e) {
            throw AIException::requestFailed($this->name(), 'connection error: '.$e->getMessage());
        }

        if ($response->failed()) {
            throw AIException::requestFailed($this->name(), $response->json('error.message') ?? $response->body());
        }

        $data = $response->json();
        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        $usage = $data['usageMetadata'] ?? [];

        if (blank($content)) {
            throw AIException::emptyResponse($this->name());
        }

        return new AIResponse(
            content: $content,
            provider: $this->name(),
            model: $model,
            tokensIn: $usage['promptTokenCount'] ?? null,
            tokensOut: $usage['candidatesTokenCount'] ?? null,
            durationMs: (int) (hrtime(true) - $start) / 1_000_000,
            raw: $data,
        );
    }
}
