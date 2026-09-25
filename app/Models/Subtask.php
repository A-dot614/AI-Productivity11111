<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtask extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'task_id',
        'title',
        'is_completed',
        'sort_order',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function markCompleted(): self
    {
        $this->is_completed = true;
        $this->completed_at = now();
        $this->save();

        return $this;
    }

    public function reopen(): self
    {
        $this->is_completed = false;
        $this->completed_at = null;
        $this->save();

        return $this;
    }
}
