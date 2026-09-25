<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Reminder\ReminderService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class ScheduleDeadlineReminders extends Command
{
    protected $signature = 'reminders:schedule-deadlines';

    protected $description = 'Schedule in-app reminders for upcoming task deadlines.';

    public function handle(ReminderService $reminders): int
    {
        $count = 0;

        User::query()->chunkById(100, function ($users) use ($reminders, &$count) {
            foreach ($users as $user) {
                $count += $reminders->scheduleDeadlineReminders(
                    $user,
                    CarbonImmutable::now(),
                    CarbonImmutable::now()->addDay(),
                );
            }
        });

        $this->info("Scheduled {$count} deadline reminder(s).");

        return self::SUCCESS;
    }
}
