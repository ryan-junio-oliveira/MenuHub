<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
            'menu_date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DailyMenuItem::class);
    }
}
