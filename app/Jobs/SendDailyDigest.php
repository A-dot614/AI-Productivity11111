<?php

namespace App\Jobs;

use App\Mail\DailyDigestMail;
use App\Models\User;
use App\Services\AI\AIAssistantService;
use App\Services\AI\Exceptions\AIException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailyDigest implements ShouldQueue
{
    use Queueable;

    public int $timeout = 120;

    public int $tries = 1;

    public function __construct(public readonly User $user) {}

    public function handle(AIAssistantService $ai): void
    {
        try {
            $digest = $ai->dailyDigest($this->user);

            Mail::to($this->user)->send(new DailyDigestMail($this->user, $digest));
        } catch (AIException $e) {
            Log::warning("Daily digest skipped for user {$this->user->id}: {$e->getMessage()}");
        }
    }
}
