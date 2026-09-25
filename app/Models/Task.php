<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
        self::STATUS_ARCHIVED,
    ];

    public const PRIORITY_LOW = 'low';

    public const PRIORITY_MEDIUM = 'medium';

    public const PRIORITY_HIGH = 'high';

    public const PRIORITY_URGENT = 'urgent';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
        self::PRIORITY_URGENT,
    ];

    public const PRIORITY_WEIGHT = [
        self::PRIORITY_LOW => 1,
        self::PRIORITY_MEDIUM => 2,
        self::PRIORITY_HIGH => 3,
        self::PRIORITY_URGENT => 4,
    ];

    public const RECURRENCE_NONE = 'none';

    public const RECURRENCE_DAILY = 'daily';

    public const RECURRENCE_WEEKLY = 'weekly';

    public const RECURRENCE_MONTHLY = 'monthly';

    public const RECURRENCES = [
        self::RECURRENCE_NONE,
        self::RECURRENCE_DAILY,
        self::RECURRENCE_WEEKLY,
        self::RECURRENCE_MONTHLY,
    ];

    protected $fillable = [
        'user_id',
        'parent_id',
        'category_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'scheduled_at',
        'estimated_minutes',
        'actual_minutes',
        'recurrence',
        'sort_order',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('sort_order');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function calendarEvent(): HasOne
    {
        return $this->hasOne(CalendarEvent::class);
    }

    public function reminders(): MorphMany
    {
        return $this->morphMany(Reminder::class, 'remindable');
    }

    public function productivityLog(): HasOne
    {
        return $this->hasOne(ProductivityLog::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->active()
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->active()
            ->whereNotNull('due_date')
            ->whereDate('due_date', today());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->active()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', today())
            ->orderBy('due_date');
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    public function scopeWithPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeInCategory(Builder $query, string $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeTagged(Builder $query, string $tagId): Builder
    {
        return $query->whereHas('tags', fn (Builder $q) => $q->where('tags.id', $tagId));
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->is_active
            && $this->due_date !== null
            && $this->due_date->isPast();
    }

    public function getIsDueTodayAttribute(): bool
    {
        return $this->due_date !== null && $this->due_date->isToday();
    }

    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_IN_PROGRESS], true);
    }

    public function getProgressAttribute(): int
    {
        $total = $this->subtasks_count ?? $this->subtasks()->count();
        if ($total === 0) {
            return $this->status === self::STATUS_COMPLETED ? 100 : 0;
        }

        $done = $this->subtasks_completed_count ?? $this->subtasks()->where('is_completed', true)->count();

        return (int) round(($done / $total) * 100);
    }

    public function markCompleted(): self
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->save();

        $this->subtasks()->where('is_completed', false)->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        return $this;
    }

    public function markInProgress(): self
    {
        $this->status = self::STATUS_IN_PROGRESS;
        $this->completed_at = null;
        $this->save();

        return $this;
    }

    public function reopen(): self
    {
        $this->status = self::STATUS_PENDING;
        $this->completed_at = null;
        $this->save();

        return $this;
    }
}
