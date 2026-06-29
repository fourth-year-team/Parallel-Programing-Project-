<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\ExportDashboardReportJob;
use Illuminate\Support\Facades\Storage;
class DashboardController extends Controller
{


public function apiExportReport(Request $request): JsonResponse
{
    if (!auth()->user()->isAdmin()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    ExportDashboardReportJob::dispatch();

    return response()->json([
        'status' => 'success',
        'message' => 'The report is being generated in the background.'
    ], 202);
}
    public function apiIndex(): JsonResponse
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $totalOrders = Order::where('status', 'completed')->count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();

        
        $monthlySales = Order::where('status', 'completed')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->reverse()
            ->values();

        
        $months = $monthlySales->map(function ($sale) {
            return date('M Y', mktime(0, 0, 0, $sale->month, 1));
        });

        $salesData = $monthlySales->pluck('total');

   
        $recentOrders = Order::with('user')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'total_products' => $totalProducts,
                'total_customers' => $totalCustomers,
                'monthly_sales' => [
                    'months' => $months,
                    'sales' => $salesData
                ],
                'recent_orders' => $recentOrders
            ]
        ]);
    }
}
