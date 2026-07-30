<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Support\Facades\Log;

class LogOrderCreation
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        Log::info('Novo pedido criado', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'restaurant_id' => $order->restaurant_id,
            'customer_id' => $order->customer_id,
            'total' => $order->total,
            'source' => $order->source,
        ]);
    }
}
