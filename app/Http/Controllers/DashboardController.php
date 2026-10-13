<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $user = auth()->user();

        // Route to role-specific dashboard
        return match (true) {
            $user->isAdmin()   => $this->adminDashboard(),
            $user->isManager() => $this->managerDashboard(),
            default            => $this->userDashboard(),
        };
    }

    // ================================================================
    //  ADMIN DASHBOARD
    // ================================================================
    private function adminDashboard()
    {
        // --- KPIs ---
        $totalRevenue   = Order::where('status', 'completed')->sum('total');
        $totalOrders    = Order::count();
        $totalCustomers = Customer::count();
        $totalProducts  = Product::count();

        // --- Revenue growth (this month vs last month) ---
        $thisMonthRevenue = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $lastMonthRevenue = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');

        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // --- Pending orders ---
        $pendingOrders = Order::where('status', 'pending')->count();

        // --- Low stock products ---
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        // --- Recent orders ---
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // --- Last 7 days revenue for mini chart ---
        $last7 = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels  = [];
        $chartRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $key  = $date->toDateString();
            $chartLabels[]  = $date->format('D');
            $chartRevenue[] = (float) ($last7[$key]->revenue ?? 0);
        }

        // --- Top 5 selling products ---
        $topProducts = \App\Models\OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as units_sold')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            'thisMonthRevenue',
            'revenueGrowth',
            'pendingOrders',
            'lowStockProducts',
            'recentOrders',
            'chartLabels',
            'chartRevenue',
            'topProducts'
        ));
    }

    // ================================================================
    //  MANAGER DASHBOARD (optional — remove if you don't need it)
    // ================================================================
    private function managerDashboard()
    {
        return view('dashboard.manager', [
            'totalOrders'    => Order::count(),
            'pendingOrders'  => Order::where('status', 'pending')->count(),
            'completedToday' => Order::where('status', 'completed')
                ->whereDate('created_at', today())->count(),
            'lowStockCount'  => Product::where('stock', '<=', 5)->count(),
            'recentOrders'   => Order::with('customer')->latest()->limit(10)->get(),
        ]);
    }

    // ================================================================
    //  USER DASHBOARD
    // ================================================================

    private function userDashboard()
    {
        $user = auth()->user();

        // Find customer using the authenticated user's email
        $customer = Customer::where('email', $user->email)->first();

        // No matching customer found
        if (!$customer) {
            return view('dashboard.user', [
                'myOrders' => 0,
                'myCompletedOrders' => 0,
                'myPendingOrders' => 0,
                'myTotalSpent' => 0,
                'recentMyOrders' => collect(),
            ]);
        }

        // Orders belonging to this customer
        $myOrders = Order::where('customer_id', $customer->id)->count();

        $myCompletedOrders = Order::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->count();

        $myPendingOrders = Order::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->count();

        $myTotalSpent = Order::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->sum('total');

        $recentMyOrders = Order::where('customer_id', $customer->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.user', compact(
            'myOrders',
            'myCompletedOrders',
            'myPendingOrders',
            'myTotalSpent',
            'recentMyOrders'
        ));
    }
}
