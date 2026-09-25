<?php

namespace App\Services\AI;

use App\Models\AIHistory;
use App\Services\AI\DataTransferObjects\AIResponse;

class AIHistoryService
{
    public function record(
        int $userId,
        string $feature,
        string $prompt,
        AIResponse $response,
        array $meta = [],
    ): AIHistory {
        return AIHistory::create([
            'user_id' => $userId,
            'feature' => $feature,
            'prompt' => $prompt,
            'response' => $response->content,
            'status' => AIHistory::STATUS_SUCCESS,
            'provider' => $response->provider,
            'model' => $response->model,
            'tokens_in' => $response->tokensIn,
            'tokens_out' => $response->tokensOut,
            'duration_ms' => $response->durationMs,
            'meta' => $meta,
        ]);
    }

    public function recordFailure(
        int $userId,
        string $feature,
        string $prompt,
        string $error,
        ?string $provider = null,
        array $meta = [],
    ): AIHistory {
        return AIHistory::create([
            'user_id' => $userId,
            'feature' => $feature,
            'prompt' => $prompt,
            'status' => AIHistory::STATUS_FAILED,
            'provider' => $provider,
            'error_message' => $error,
            'meta' => $meta,
        ]);
    }
}
