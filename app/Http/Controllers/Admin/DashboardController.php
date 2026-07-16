<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard.
     *
     * Stats are cached for 5 minutes to reduce PostgreSQL load.
     * Cache invalidates automatically at midnight (date-scoped keys).
     * Recent orders cached for 60s for near-real-time feel.
     *
     * To force refresh: php artisan cache:clear
     */
    public function index(): View
    {
        $today = now()->toDateString();

        // Aggregate stats — cached 5 minutes (acceptable lag for admin overview)
        $totalSalesToday = Cache::remember("dashboard_sales_{$today}", 300, function () use ($today) {
            return Order::whereDate('created_at', $today)
                ->whereIn('status', ['paid', 'processing', 'shipped', 'delivered', 'completed'])
                ->sum('total');
        });

        $ordersTodayCount = Cache::remember("dashboard_orders_count_{$today}", 300, function () use ($today) {
            return Order::whereDate('created_at', $today)->count();
        });

        $pendingOrdersCount = Cache::remember('dashboard_pending_orders', 300, function () {
            return Order::where('status', 'pending')->count();
        });

        $totalProducts = Cache::remember('dashboard_total_products', 300, function () {
            return Product::count();
        });

        $totalCustomers = Cache::remember('dashboard_total_customers', 300, function () {
            return User::where('role', 'customer')->count();
        });

        // Recent orders — cached 60 seconds (near-real-time)
        $recentOrders = Cache::remember('dashboard_recent_orders', 60, function () {
            return Order::with('user')
                ->latest()
                ->take(5)
                ->get();
        });

        return view('admin.dashboard', compact(
            'totalSalesToday',
            'ordersTodayCount',
            'pendingOrdersCount',
            'totalProducts',
            'totalCustomers',
            'recentOrders'
        ));
    }
}

