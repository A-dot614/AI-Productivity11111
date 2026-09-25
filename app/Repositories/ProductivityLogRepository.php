<?php

namespace App\Repositories;

use App\Models\ProductivityLog;
use App\Repositories\Contracts\ProductivityLogRepositoryInterface;

class ProductivityLogRepository implements ProductivityLogRepositoryInterface
{
    public function forUserAndDate(int $userId, $date): ?ProductivityLog
    {
        return ProductivityLog::query()
            ->where('user_id', $userId)
            ->whereDate('log_date', $date)
            ->first();
    }

    public function incrementCompleted(int $userId, $date, int $taskId, int $focusMinutes = 0): ProductivityLog
    {
        $log = ProductivityLog::query()
            ->where('user_id', $userId)
            ->whereDate('log_date', $date)
            ->first();

        if (! $log) {
            $log = ProductivityLog::query()->create([
                'user_id' => $userId,
                'log_date' => $date,
                'focus_minutes' => 0,
                'completed_tasks' => 0,
                'planned_tasks' => 0,
            ]);
        }

        $log->increment('completed_tasks');

        if ($focusMinutes > 0) {
            $log->increment('focus_minutes', $focusMinutes);
        }

        return $log->refresh();
    }

    public function decrementCompleted(int $userId, $date, int $taskId): void
    {
        $log = $this->forUserAndDate($userId, $date);

        if ($log && $log->completed_tasks > 0) {
            $log->decrement('completed_tasks');
        }
    }
}
