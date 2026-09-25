<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reminder extends Model
{
    use HasFactory, HasUuids;

    public const CHANNEL_APP = 'app';

    public const CHANNEL_EMAIL = 'email';

    public const CHANNEL_BOTH = 'both';

    protected $fillable = [
        'user_id',
        'remindable_type',
        'remindable_id',
        'remind_at',
        'channel',
        'subject',
        'message',
        'is_sent',
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function remindable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_sent', false)->where('remind_at', '<=', now());
    }

    public function scopeDueSoon(Builder $query, $from, $to): Builder
    {
        return $query->where('is_sent', false)
            ->whereBetween('remind_at', [$from, $to]);
    }

    public function markSent(): self
    {
        $this->update(['is_sent' => true, 'sent_at' => now()]);

        return $this;
    }
}
