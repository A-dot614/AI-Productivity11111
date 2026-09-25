<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductivityLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'task_id',
        'log_date',
        'focus_minutes',
        'completed_tasks',
        'planned_tasks',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function scopeOnDate(Builder $query, $date): Builder
    {
        return $query->whereDate('log_date', $date);
    }

    public function scopeBetween(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('log_date', [$from, $to]);
    }

    public function getCompletionRateAttribute(): int
    {
        if ($this->planned_tasks <= 0) {
            return 0;
        }

        return (int) round(($this->completed_tasks / $this->planned_tasks) * 100);
    }
}
