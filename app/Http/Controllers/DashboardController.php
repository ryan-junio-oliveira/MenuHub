<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $stats = $this->dashboardService->getTodayStats($restaurantId);
        $chartData = $this->dashboardService->getChartData($restaurantId);
        $statusDistribution = $this->dashboardService->getStatusDistribution($restaurantId);
        $topDishes = $this->dashboardService->getTopDishes($restaurantId);
        $latestOrders = $this->dashboardService->getLatestOrders($restaurantId);

        return view('dashboard.index', compact(
            'stats',
            'chartData',
            'statusDistribution',
            'topDishes',
            'latestOrders'
        ));
    }

    public function rootIndex()
    {
        $restaurantCount = \App\Models\Restaurant::count();
        $userCount = \App\Models\User::count();
        $totalOrders = \App\Models\Order::count();
        $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('total');

        $recentRestaurants = \App\Models\Restaurant::withCount('users')
            ->latest()
            ->limit(6)
            ->get();

        $statusCounts = \App\Models\Order::selectRaw("status, count(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('root.dashboard', compact(
            'restaurantCount',
            'userCount',
            'totalOrders',
            'totalRevenue',
            'recentRestaurants',
            'statusCounts',
        ));
    }
}
