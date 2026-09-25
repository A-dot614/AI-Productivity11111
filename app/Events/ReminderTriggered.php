<?php

namespace App\Events;

use App\Models\Reminder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReminderTriggered
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Reminder $reminder) {}
}
