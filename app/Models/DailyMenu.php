<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'menu_date',
        'title',
        'notes',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'menu_date' => 'date:Y-m-d',
            'is_published' => 'boolean',
        ];
    }

    public function getDateAttribute(): mixed
    {
        return $this->menu_date;
    }

    public function getMaxSelectionsAttribute(): int
    {
        return $this->items->min('max_selections') ?? 3;
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DailyMenuItem::class);
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(MenuOption::class, 'daily_menu_option')
            ->withTimestamps();
    }
}
