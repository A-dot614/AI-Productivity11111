<?php

namespace App\Console\Commands;

use App\Services\Reminder\ReminderService;
use Illuminate\Console\Command;

class ProcessReminders extends Command
{
    protected $signature = 'reminders:process';

    protected $description = 'Dispatch all reminders that are due and send their notifications.';

    public function handle(ReminderService $reminders): int
    {
        $count = $reminders->processDue();

        $this->info("Processed {$count} reminder(s).");

        return self::SUCCESS;
    }
}
