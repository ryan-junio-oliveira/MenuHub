<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'dish_category_id',
        'name',
        'description',
        'image',
        'price_small',
        'price_medium',
        'price_large',
        'is_gourmet',
        'max_selections',
        'is_available',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_small' => 'decimal:2',
            'price_medium' => 'decimal:2',
            'price_large' => 'decimal:2',
            'is_gourmet' => 'boolean',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
            'max_selections' => 'integer',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DishCategory::class, 'dish_category_id');
    }
}
