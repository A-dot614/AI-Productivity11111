<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('reminders:process')->everyMinute()->withoutOverlapping();
Schedule::command('reminders:schedule-deadlines')->everyThirtyMinutes()->withoutOverlapping();
Schedule::command('digest:send')->dailyAt('07:00')->withoutOverlapping();
