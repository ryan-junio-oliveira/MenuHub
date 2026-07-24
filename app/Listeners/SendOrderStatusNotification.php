<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Notifications\OrderStatusUpdated;

class SendOrderStatusNotification
{
    public function handle(OrderStatusChanged $event): void
    {
        $customer = $event->order->customer;
        if ($customer && method_exists($customer, 'notify')) {
            $customer->notify(new OrderStatusUpdated($event->order));
        }
    }
}
