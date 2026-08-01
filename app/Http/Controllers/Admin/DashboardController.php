<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        // Get statistics
        $stats = $this->dashboardService->getDashboardStats();
        
        // Get chart data
        $salesChart = $this->dashboardService->getSalesChartData();
        $purchaseChart = $this->dashboardService->getPurchaseChartData();
        
        // Get top products
        $topProducts = $this->dashboardService->getTopSellingProducts(5);
        
        // Get recent records
        $recentSales = $this->dashboardService->getRecentSales(5);
        $recentRepairs = $this->dashboardService->getRecentRepairs(5);
        $recentPurchases = $this->dashboardService->getRecentPurchases(5);

        return view('admin.dashboard.index', compact(
            'stats',
            'salesChart',
            'purchaseChart',
            'topProducts',
            'recentSales',
            'recentRepairs',
            'recentPurchases'
        ));
    }

    public function chartData(Request $request)
    {
        $type = $request->get('type', 'sales');
        
        if ($type === 'sales') {
            return response()->json($this->dashboardService->getSalesChartData());
        } elseif ($type === 'purchase') {
            return response()->json($this->dashboardService->getPurchaseChartData());
        }

        return response()->json([]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        if (empty($query)) {
            return response()->json([]);
        }
        
        $results = $this->dashboardService->globalSearch($query);
        return response()->json($results);
    }
}