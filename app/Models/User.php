<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_USER = 'user';

    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'timezone',
        'theme',
        'productivity_goal_minutes',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function subtasks(): HasManyThrough
    {
        return $this->hasManyThrough(Subtask::class, Task::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function productivityLogs(): HasMany
    {
        return $this->hasMany(ProductivityLog::class);
    }

    public function aiHistories(): HasMany
    {
        return $this->hasMany(AIHistory::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/'.$this->avatar)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=6366f1&color=fff&size=128';
    }
}
