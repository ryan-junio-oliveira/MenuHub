<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Notifications\OrderStatusUpdated;

class SendOrderStatusNotification
{
    public function handle(OrderStatusChanged $event): void
    {
        if ($event->order->customer) {
            $event->order->customer->notify(new OrderStatusUpdated($event->order));
        }
    }
}
