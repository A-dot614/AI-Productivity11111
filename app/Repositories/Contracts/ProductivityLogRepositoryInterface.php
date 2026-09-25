<?php

namespace App\Repositories\Contracts;

use App\Models\ProductivityLog;

interface ProductivityLogRepositoryInterface
{
    public function forUserAndDate(int $userId, $date): ?ProductivityLog;

    public function incrementCompleted(int $userId, $date, int $taskId, int $focusMinutes = 0): ProductivityLog;

    public function decrementCompleted(int $userId, $date, int $taskId): void;
}
