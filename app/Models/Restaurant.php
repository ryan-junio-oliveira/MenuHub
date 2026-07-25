<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'logo',
        'cover',
        'pix_key',
        'whatsapp_number',
        'whatsapp_api_token',
        'whatsapp_phone_id',
        'whatsapp_business_account_id',
        'delivery_fee',
        'minimum_order',
        'opening_hours',
        'is_active',
        'plan_id',
        'subscription_status',
        'trial_ends_at',
        'paid_until',
    ];

    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
            'delivery_fee' => 'decimal:2',
            'minimum_order' => 'decimal:2',
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
            'paid_until' => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function dishCategories(): HasMany
    {
        return $this->hasMany(DishCategory::class);
    }

    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    public function dailyMenus(): HasMany
    {
        return $this->hasMany(DailyMenu::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }
}
