<?php

namespace App\Services\Setting;

use App\Models\Setting;

class SettingService
{
    /**
     * Retrieve a setting value with an optional default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    /**
     * Store a setting value.
     */
    public function set(string $key, mixed $value, string $group = 'system', bool $isPublic = false): Setting
    {
        return Setting::set($key, $value, $group, $isPublic);
    }

    /**
     * Batch-store settings.
     *
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values, string $group = 'system', bool $isPublic = false): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $group, $isPublic);
        }
    }

    /**
     * Retrieve a batch of settings as a key/value map.
     *
     * @param  list<string>  $keys
     * @return array<string, mixed>
     */
    public function getMany(array $keys): array
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }
}
