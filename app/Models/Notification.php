<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory, HasUuids;

    public const TYPE_REMINDER = 'reminder';

    public const TYPE_DEADLINE = 'deadline';

    public const TYPE_AI = 'ai';

    public const TYPE_SYSTEM = 'system';

    public const TYPE_TASK = 'task';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'icon',
        'link',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function markAsRead(): self
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }

        return $this;
    }

    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }
}
