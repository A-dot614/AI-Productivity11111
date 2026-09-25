<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    public const GROUP_AI = 'ai';

    public const GROUP_SYSTEM = 'system';

    public const GROUP_NOTIFICATION = 'notification';

    protected $fillable = [
        'key',
        'value',
        'group',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Retrieve a setting value with an optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::rememberForever('setting:'.$key, function () use ($key, $default) {
            $setting = static::query()->where('key', $key)->first();

            return $setting?->value ?? $default;
        });

        if (str_ends_with($key, 'api_key')) {
            $value = static::decryptSecret($value);
        }

        return $value;
    }

    /**
     * Store a setting value and invalidate the cache.
     */
    public static function set(string $key, mixed $value, string $group = 'system', bool $isPublic = false): Setting
    {
        Cache::forget('setting:'.$key);

        if (str_ends_with($key, 'api_key') && is_string($value) && filled($value)) {
            $value = encrypt($value);
        }

        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'is_public' => $isPublic]
        );
    }

    public static function flushCache(string $key): void
    {
        Cache::forget('setting:'.$key);
    }

    /**
     * Decrypt a secret value, tolerating legacy plaintext values.
     */
    protected static function decryptSecret(mixed $value): mixed
    {
        if (! is_string($value) || blank($value)) {
            return $value;
        }

        try {
            return decrypt($value);
        } catch (\Throwable) {
            return $value;
        }
    }
}
