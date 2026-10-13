<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $from = $request->input('from', now()->subDays(30)->toDateString());
        $to   = $request->input('to',   now()->toDateString());

        $orders = Order::with('user')
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->latest()
            ->paginate(20);

        $totals = [
            'revenue' => Order::where('status', 'completed')
                ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
                ->sum('total'),
            'orders'  => Order::whereBetween('created_at', [$from, $to . ' 23:59:59'])->count(),
            'avg'     => Order::where('status', 'completed')
                ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
                ->avg('total') ?? 0,
        ];

        // Sales by day for chart
        $byDay = Order::where('status', 'completed')
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.sales', compact('orders', 'totals', 'byDay', 'from', 'to'));
    }

    public function inventory()
    {
        $products = Product::with('category')
            ->orderBy('stock')
            ->get();

        $stats = [
            'total_products' => $products->count(),
            'total_stock'    => $products->sum('stock'),
            'total_value'    => $products->sum(fn($p) => $p->stock * $p->price),
            'low_stock'      => $products->where('stock', '<=', 5)->count(),
        ];

        $byCategory = Category::withCount('products')
            ->withSum('products as total_stock', 'stock')
            ->get();

        return view('reports.inventory', compact('products', 'stats', 'byCategory'));
    }

    public function customers(Request $request)
    {
        $from = $request->input('from', now()->subDays(90)->toDateString());
        $to   = $request->input('to',   now()->toDateString());

        $customers = Customer::withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->orderByDesc('total_spent')
            ->paginate(20);

        $stats = [
            'total'      => Customer::count(),
            'new_period' => Customer::whereBetween('created_at', [$from, $to . ' 23:59:59'])->count(),
            'avg_spent'  => Customer::withSum('orders as s', 'total')->get()->avg('s') ?? 0,
        ];

        return view('reports.customers', compact('customers', 'stats', 'from', 'to'));
    }
}
