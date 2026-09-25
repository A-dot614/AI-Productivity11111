<?php

namespace App\Console\Commands;

use App\Jobs\SendDailyDigest;
use App\Models\User;
use App\Services\Setting\SettingService;
use Illuminate\Console\Command;

class SendDailyDigests extends Command
{
    protected $signature = 'digest:send';

    protected $description = 'Generate and send the AI-powered daily morning digest to enabled users.';

    public function handle(SettingService $settings): int
    {
        if (! (bool) $settings->get('daily_digest_enabled', false)) {
            $this->warn('Daily digest is disabled in settings.');

            return self::SUCCESS;
        }

        $count = 0;

        User::query()->chunkById(100, function ($users) use (&$count) {
            foreach ($users as $user) {
                SendDailyDigest::dispatch($user);
                $count++;
            }
        });

        $this->info("Queued {$count} daily digest(s).");

        return self::SUCCESS;
    }
}
