<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DemandPredictionService
{
    public function predictWeeklyDemand(int $restaurantId): array
    {
        $weeksBack = 4;
        $endDate = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        $startDate = Carbon::now()->subWeeks($weeksBack)->startOfWeek(Carbon::MONDAY);

        $historicalData = OrderItem::whereHas('order', function ($q) use ($restaurantId, $startDate, $endDate) {
            $q->where('restaurant_id', $restaurantId)
                ->where('status', '!=', Order::STATUS_CANCELED)
                ->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->select(
                'dish_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('COUNT(DISTINCT DATE(orders.ordered_at)) as days_ordered')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->groupBy('dish_name')
            ->orderByDesc('total_qty')
            ->get();

        $dayOfWeekMultipliers = $this->getDayOfWeekMultipliers($restaurantId);

        $predictions = $historicalData->map(function ($item) use ($weeksBack) {
            $dailyAvg = $item->total_qty / max($item->days_ordered, 1);
            return [
                'dish_name' => $item->dish_name,
                'avg_per_day' => round($dailyAvg, 1),
                'total_historical' => (int) $item->total_qty,
                'days_ordered' => (int) $item->days_ordered,
                'predicted_weekly' => round($dailyAvg * 7),
            ];
        })->values()->toArray();

        $weeklyTotal = collect($predictions)->sum('predicted_weekly');
        $lastWeekTotal = OrderItem::whereHas('order', function ($q) use ($restaurantId) {
            $q->where('restaurant_id', $restaurantId)
                ->where('status', '!=', Order::STATUS_CANCELED)
                ->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
        })->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->sum('order_items.quantity');

        return [
            'predictions' => $predictions,
            'weekly_total_predicted' => $weeklyTotal,
            'last_week_total' => (int) $lastWeekTotal,
            'trend_pct' => $lastWeekTotal > 0 ? round(($weeklyTotal - $lastWeekTotal) / $lastWeekTotal * 100, 1) : 0,
            'day_multipliers' => $dayOfWeekMultipliers,
            'week_of' => Carbon::now()->startOfWeek()->format('d/m/Y'),
        ];
    }

    public function predictInsufficientStock(int $restaurantId): array
    {
        return [];
    }

    private function getDayOfWeekMultipliers(int $restaurantId): array
    {
        $days = Order::where('restaurant_id', $restaurantId)
            ->where('status', '!=', Order::STATUS_CANCELED)
            ->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()])
            ->select(DB::raw("(CAST(strftime('%w', created_at) AS INTEGER) + 1) as day_num"), DB::raw('COUNT(*) as total'))
            ->groupBy('day_num')
            ->pluck('total', 'day_num');

        $maxDay = $days->max() ?: 1;
        $dayNames = ['', 'Domingo', 'Segunda', 'Terca', 'Quarta', 'Quinta', 'Sexta', 'Sabado'];

        $multipliers = [];
        foreach (range(1, 7) as $i) {
            $count = $days[$i] ?? 0;
            $multipliers[] = [
                'day' => $dayNames[$i],
                'orders' => $count,
                'pct_of_peak' => $maxDay > 0 ? round($count / $maxDay * 100) : 0,
            ];
        }

        return $multipliers;
    }
}
