<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use Illuminate\Support\Facades\Log;

class LogOrderActivity
{
    public function handle(OrderStatusChanged $event): void
    {
        Log::debug('Order status changed', [
            'restaurant_id' => $event->order->restaurant_id,
            'order_id' => $event->order->id,
            'order_number' => $event->order->order_number,
            'from' => $event->oldStatus,
            'to' => $event->newStatus,
        ]);
    }
}
