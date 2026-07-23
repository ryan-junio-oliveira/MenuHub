<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {}

    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $period = $request->get('period', 'monthly');
        $year = $request->get('year', now()->year);
        $limit = $request->get('limit', 10);

        $report = $this->reportService->getRevenueReport($restaurantId, $period, $year);
        $dishes = $this->reportService->getBestSellingDishes($restaurantId, $limit);
        $combinations = $this->reportService->getPopularCombinations($restaurantId, $limit);
        $peakHours = $this->reportService->getPeakHours($restaurantId);

        return view('reports.index', compact('report', 'period', 'year', 'dishes', 'limit', 'combinations', 'peakHours'));
    }

    public function revenue(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $period = $request->get('period', 'monthly');
        $year = $request->get('year', now()->year);

        $report = $this->reportService->getRevenueReport($restaurantId, $period, $year);

        return view('reports.revenue', compact('report', 'period', 'year'));
    }

    public function dishes(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $limit = $request->get('limit', 10);

        $dishes = $this->reportService->getBestSellingDishes($restaurantId, $limit);

        return view('reports.dishes', compact('dishes', 'limit'));
    }

    public function combinations(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $limit = $request->get('limit', 10);

        $combinations = $this->reportService->getPopularCombinations($restaurantId, $limit);

        return view('reports.combinations', compact('combinations', 'limit'));
    }

    public function hours(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $peakHours = $this->reportService->getPeakHours($restaurantId);

        return view('reports.hours', compact('peakHours'));
    }
}
