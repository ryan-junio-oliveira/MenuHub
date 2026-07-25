<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'max_users',
        'max_orders_monthly',
        'features',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'max_users' => 'integer',
            'max_orders_monthly' => 'integer',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static array $featureMap = [
        'essential' => ['basic_menu', 'whatsapp_bot', 'pix_payment'],
        'pro' => ['basic_menu', 'whatsapp_bot', 'pix_payment', 'reports', 'delivery_management', 'priority_support'],
        'enterprise' => ['basic_menu', 'whatsapp_bot', 'pix_payment', 'reports', 'delivery_management', 'priority_support', 'api_access', 'unlimited_orders', 'dedicated_manager', 'support_24h'],
    ];

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, self::$featureMap[$this->slug] ?? []);
    }

    public static function featureList(): array
    {
        return self::$featureMap;
    }

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
