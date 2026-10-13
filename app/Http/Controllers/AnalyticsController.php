<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // return view('analytics.index', [
        //     'totalRevenue' => Order::where('status', 'completed')->sum('total') ?? 45231,
        //     'totalOrders'  => Order::count() ?? 1234,
        //     'totalCustomers' => \App\Models\Customer::count() ?? 892,
        //     'conversionRate' => '3.24%',
        // ]);

        // -------- Date range filter --------
        $range = $request->input('range', '30');   // 7, 30, 90, 365, all
        $from  = match ($range) {
            '7'   => now()->subDays(7),
            '30'  => now()->subDays(30),
            '90'  => now()->subDays(90),
            '365' => now()->subYear(),
            default => now()->subYears(10),
        };
        $to = now();

        // -------- KPI Cards --------
        $totalRevenue   = Order::where('status', 'completed')
            ->whereBetween('created_at', [$from, $to])
            ->sum('total');

        $prevFrom = $from->copy()->subDays($from->diffInDays($to));
        $prevTo   = $from->copy();
        $prevRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$prevFrom, $prevTo])
            ->sum('total');

        $revenueChange = $prevRevenue > 0
            ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : 0;

        $totalOrders    = Order::whereBetween('created_at', [$from, $to])->count();
        $prevOrders     = Order::whereBetween('created_at', [$prevFrom, $prevTo])->count();
        $ordersChange   = $prevOrders > 0
            ? round((($totalOrders - $prevOrders) / $prevOrders) * 100, 1)
            : 0;

        $totalCustomers = Customer::whereBetween('created_at', [$from, $to])->count();
        $prevCustomers  = Customer::whereBetween('created_at', [$prevFrom, $prevTo])->count();
        $customersChange = $prevCustomers > 0
            ? round((($totalCustomers - $prevCustomers) / $prevCustomers) * 100, 1)
            : 0;

        $avgOrderValue  = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $prevAov        = $prevOrders > 0 ? $prevRevenue / $prevOrders : 0;
        $aovChange      = $prevAov > 0
            ? round((($avgOrderValue - $prevAov) / $prevAov) * 100, 1)
            : 0;

        // -------- Revenue over time (line chart) --------
        $revenueByDay = Order::where('status', 'completed')
            ->whereBetween('created_at', [$from, $to])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill gaps so chart isn't broken
        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders  = [];
        $cursor = $from->copy();
        $map = $revenueByDay->keyBy('date');

        while ($cursor <= $to) {
            $key = $cursor->toDateString();
            $chartLabels[]  = $cursor->format('M d');
            $chartRevenue[] = (float) ($map[$key]->revenue ?? 0);
            $chartOrders[]  = (int)   ($map[$key]->orders ?? 0);
            $cursor->addDay();

            // prevent huge loops
            if (count($chartLabels) > 400) break;
        }

        // -------- Orders by status (doughnut) --------
        $ordersByStatus = Order::whereBetween('created_at', [$from, $to])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // -------- Top products by revenue (bar) --------
        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$from, $to])
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // -------- Revenue by category (pie) --------
        $revenueByCategory = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$from, $to])
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        // -------- New vs Returning customers (bar) --------
        $newCustomers = Customer::whereBetween('created_at', [$from, $to])->count();
        $returningCustomers = Customer::whereHas('orders', function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from, $to]);
        })
            ->where('created_at', '<', $from)
            ->count();

        // -------- Recent orders table --------
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // -------- Low stock products --------
        $lowStock = Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        return view('analytics.index', compact(
            'range',
            'totalRevenue',
            'revenueChange',
            'totalOrders',
            'ordersChange',
            'totalCustomers',
            'customersChange',
            'avgOrderValue',
            'aovChange',
            'chartLabels',
            'chartRevenue',
            'chartOrders',
            'ordersByStatus',
            'topProducts',
            'revenueByCategory',
            'newCustomers',
            'returningCustomers',
            'recentOrders',
            'lowStock'
        ));
    }
}
