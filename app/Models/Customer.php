<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Customer extends Model
{
    use HasFactory;
    use LogsActivity;
    use Notifiable;

    protected $fillable = [
        'restaurant_id',
        'name',
        'phone',
        'email',
        'address',
        'notes',
        'total_orders',
        'total_spent',
    ];

    protected function casts(): array
    {
        return [
            'total_orders' => 'integer',
            'total_spent' => 'decimal:2',
            'phone' => 'encrypted',
            'email' => 'encrypted',
            'address' => 'encrypted',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'phone', 'email', 'total_orders', 'total_spent'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(CustomerTag::class, 'customer_customer_tag');
    }

    public function scopeActive($query)
    {
        return $query->whereHas('orders', fn($q) => $q->whereIn('status', ['pending', 'received', 'preparing', 'out_for_delivery']));
    }

    public function anonymize(): void
    {
        $this->update([
            'name' => '[Removido]',
            'phone' => null,
            'email' => null,
            'address' => null,
            'notes' => null,
        ]);
    }
}
