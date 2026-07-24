<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyMenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_menu_id',
        'dish_id',
        'price_small',
        'price_medium',
        'price_large',
        'max_selections',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price_small' => 'decimal:2',
            'price_medium' => 'decimal:2',
            'price_large' => 'decimal:2',
            'is_available' => 'boolean',
            'max_selections' => 'integer',
        ];
    }

    public function dailyMenu(): BelongsTo
    {
        return $this->belongsTo(DailyMenu::class);
    }

    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }
}
