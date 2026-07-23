<?php

namespace App\Services;

use App\Models\DailyMenu;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getTodayStats(int $restaurantId): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        return Cache::remember("dashboard.stats.{$restaurantId}.{$today->format('Y-m-d')}", 60, function () use ($restaurantId, $today, $yesterday) {
            $ordersToday = Order::where('restaurant_id', $restaurantId)
                ->whereDate('ordered_at', $today)
                ->get();

            $ordersYesterday = Order::where('restaurant_id', $restaurantId)
                ->whereDate('ordered_at', $yesterday)
                ->get();

            $revenueToday = $ordersToday->where('status', '!=', Order::STATUS_CANCELED)->sum('total');
            $revenueYesterday = $ordersYesterday->where('status', '!=', Order::STATUS_CANCELED)->sum('total');

            $completedOrdersToday = $ordersToday->where('status', Order::STATUS_COMPLETED)->count();
            $completedOrdersYesterday = $ordersYesterday->where('status', Order::STATUS_COMPLETED)->count();

            $pendingOrders = $ordersToday->where('status', Order::STATUS_RECEIVED)->count();
            $pendingOrdersYesterday = $ordersYesterday->where('status', Order::STATUS_RECEIVED)->count();

            $ordersCountToday = $ordersToday->count();
            $ordersCountYesterday = $ordersYesterday->count();

            $averageTicket = $ordersCountToday > 0 ? $revenueToday / $ordersCountToday : 0;
            $averageTicketYesterday = $ordersCountYesterday > 0 ? $revenueYesterday / $ordersCountYesterday : 0;

            $activeMenu = DailyMenu::where('restaurant_id', $restaurantId)
                ->where('menu_date', $today)
                ->where('is_published', true)
                ->first();

            $customersServed = $ordersToday->whereIn('status', [Order::STATUS_COMPLETED, Order::STATUS_OUT_FOR_DELIVERY])->count();

            return [
                'orders_count' => $ordersCountToday,
                'orders_yesterday' => $ordersCountYesterday,
                'revenue' => $revenueToday,
                'revenue_yesterday' => $revenueYesterday,
                'pending_orders' => $pendingOrders,
                'pending_yesterday' => $pendingOrdersYesterday,
                'active_menu' => $activeMenu ? true : false,
                'average_ticket' => $averageTicket,
                'average_ticket_yesterday' => $averageTicketYesterday,
                'customers_served' => $customersServed,
                'customers_yesterday' => $completedOrdersYesterday,
                'completed_today' => $completedOrdersToday,
                'completed_yesterday' => $completedOrdersYesterday,
            ];
        });
    }

    public function getChartData(int $restaurantId, int $days = 14): array
    {
        $dates = collect();
        $ordersChart = collect();
        $revenueChart = collect();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates->push($date->format('d/m'));

            $dayOrders = Order::where('restaurant_id', $restaurantId)
                ->whereDate('ordered_at', $date)
                ->where('status', '!=', Order::STATUS_CANCELED)
                ->count();

            $dayRevenue = Order::where('restaurant_id', $restaurantId)
                ->whereDate('ordered_at', $date)
                ->where('status', '!=', Order::STATUS_CANCELED)
                ->sum('total');

            $ordersChart->push($dayOrders);
            $revenueChart->push((float) $dayRevenue);
        }

        return [
            'dates' => $dates,
            'orders' => $ordersChart,
            'revenue' => $revenueChart,
        ];
    }

    public function getStatusDistribution(int $restaurantId): array
    {
        $today = Carbon::today();

        $statuses = Order::where('restaurant_id', $restaurantId)
            ->whereDate('ordered_at', $today)
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'received' => $statuses[Order::STATUS_RECEIVED] ?? 0,
            'preparing' => $statuses[Order::STATUS_PREPARING] ?? 0,
            'out_for_delivery' => $statuses[Order::STATUS_OUT_FOR_DELIVERY] ?? 0,
            'completed' => $statuses[Order::STATUS_COMPLETED] ?? 0,
            'canceled' => $statuses[Order::STATUS_CANCELED] ?? 0,
        ];
    }

    public function getTopDishes(int $restaurantId, int $limit = 5): array
    {
        return OrderItem::whereHas('order', function ($q) use ($restaurantId) {
            $q->where('restaurant_id', $restaurantId)
                ->whereDate('ordered_at', Carbon::today());
        })
            ->select('dish_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_rev'))
            ->groupBy('dish_name')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getLatestOrders(int $restaurantId, int $limit = 5): array
    {
        return Order::where('restaurant_id', $restaurantId)
            ->whereDate('ordered_at', Carbon::today())
            ->with('customer')
            ->orderByDesc('ordered_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function clearCache(int $restaurantId): void
    {
        $today = Carbon::today()->format('Y-m-d');
        Cache::forget("dashboard.stats.{$restaurantId}.{$today}");
    }
}
