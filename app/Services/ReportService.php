<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getRevenueReport(int $restaurantId, string $period = 'monthly', ?int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;

        $query = Order::where('restaurant_id', $restaurantId)
            ->whereYear('ordered_at', $year)
            ->where('status', '!=', Order::STATUS_CANCELED);

        if ($period === 'monthly') {
            $results = (clone $query)
                ->select(
                    DB::raw('MONTH(ordered_at) as month'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(total) as revenue'),
                    DB::raw('AVG(total) as average_order')
                )
                ->groupBy(DB::raw('MONTH(ordered_at)'))
                ->orderBy(DB::raw('MONTH(ordered_at)'))
                ->get();

            $months = collect(range(1, 12))->mapWithKeys(fn($m) => [
                $m => ['month' => $m, 'total_orders' => 0, 'revenue' => 0, 'average_order' => 0]
            ]);

            foreach ($results as $row) {
                $months[$row->month] = [
                    'month' => $row->month,
                    'total_orders' => $row->total_orders,
                    'revenue' => (float) $row->revenue,
                    'average_order' => (float) $row->average_order,
                ];
            }

            return $months->values()->toArray();
        }

        return [];
    }

    public function getBestSellingDishes(int $restaurantId, int $limit = 10): array
    {
        return OrderItem::whereHas('order', function ($q) use ($restaurantId) {
            $q->where('restaurant_id', $restaurantId)
                ->where('status', '!=', Order::STATUS_CANCELED);
        })
            ->select('dish_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('dish_name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getPeakHours(int $restaurantId): array
    {
        return Order::where('restaurant_id', $restaurantId)
            ->where('status', '!=', Order::STATUS_CANCELED)
            ->select(DB::raw('HOUR(ordered_at) as hour'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('HOUR(ordered_at)'))
            ->orderBy('hour')
            ->get()
            ->toArray();
    }

    public function getPopularCombinations(int $restaurantId, int $limit = 10): array
    {
        return DB::select("
            SELECT 
                GROUP_CONCAT(DISTINCT oi1.dish_name ORDER BY oi1.dish_name SEPARATOR ' + ') as combination,
                COUNT(*) as frequency
            FROM order_items oi1
            INNER JOIN orders o ON oi1.order_id = o.id
            INNER JOIN order_items oi2 ON oi1.order_id = oi2.order_id AND oi1.id < oi2.id
            WHERE o.restaurant_id = ?
            AND o.status != ?
            GROUP BY oi1.order_id
            HAVING COUNT(*) > 1
            ORDER BY frequency DESC
            LIMIT ?
        ", [$restaurantId, Order::STATUS_CANCELED, $limit]);
    }
}
