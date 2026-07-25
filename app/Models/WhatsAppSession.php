<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'customer_phone',
        'customer_name',
        'step',
        'data',
        'menu_id',
        'is_completed',
        'last_interaction_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_completed' => 'boolean',
            'last_interaction_at' => 'datetime',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(DailyMenu::class, 'menu_id');
    }
}
