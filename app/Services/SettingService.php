<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function get(int $restaurantId, string $key, mixed $default = null): mixed
    {
        $setting = Setting::where('restaurant_id', $restaurantId)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->value : $default;
    }

    public function set(int $restaurantId, string $key, mixed $value, string $group = 'general'): Setting
    {
        return Setting::updateOrCreate(
            ['restaurant_id' => $restaurantId, 'key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    public function getGroup(int $restaurantId, string $group): array
    {
        return Setting::where('restaurant_id', $restaurantId)
            ->where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    public function setGroup(int $restaurantId, string $group, array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->set($restaurantId, $key, $value, $group);
        }
    }
}
