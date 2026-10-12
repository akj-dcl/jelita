<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Stats
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $revenueLast30Days = Order::where('created_at', '>=', $thirtyDaysAgo)
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->sum('subtotal');
        $lowStockCount = Product::where('stock', '<=', 5)->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $pendingPayment = Order::where('status', 'pending_payment')->count();
        $processing = Order::where('status', 'processing')->count();

        // Recent Orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Top Products
        $topProducts = OrderItem::with(['product' => function($q) {
                $q->select('id', 'name', 'price', 'stock', 'slug');
            }])
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Revenue Chart Data (Last 30 days)
        // Ensure dates without orders have 0 revenue if possible, or frontend can handle it
        $revenueChartData = Order::where('created_at', '>=', $thirtyDaysAgo)
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(subtotal) as daily_revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing days in last 30 days for continuous chart
        $chartDates = [];
        $chartRevenues = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $dateString = Carbon::now()->subDays($i)->format('Y-m-d');
            $revenue = $revenueChartData->firstWhere('date', $dateString);
            
            $chartDates[] = Carbon::now()->subDays($i)->format('M d');
            $chartRevenues[] = $revenue ? (float) $revenue->daily_revenue : 0;
        }

        // Most Viewed Products
        $mostViewedProducts = Product::where('is_published', true)
            ->where('view_count', '>', 0)
            ->orderByDesc('view_count')
            ->take(5)
            ->get();

        // Recent Reviews
        $recentReviews = Review::with(['user', 'product'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'todayOrders' => $todayOrders,
                'revenueLast30Days' => (float) $revenueLast30Days,
                'lowStockCount' => $lowStockCount,
                'totalCustomers' => $totalCustomers,
                'pendingPayment' => $pendingPayment,
                'processing' => $processing,
            ],
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'mostViewedProducts' => $mostViewedProducts,
            'recentReviews' => $recentReviews,
            'chart' => [
                'categories' => $chartDates,
                'data' => $chartRevenues
            ]
        ]);
    }
}
