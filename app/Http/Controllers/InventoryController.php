<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;


class InventoryController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter by stock status
        $filter = $request->input('filter', 'all');
        if ($filter === 'low') {
            $query->whereBetween('stock', [1, 5]);
        } elseif ($filter === 'out') {
            $query->where('stock', '<=', 0);
        } elseif ($filter === 'ok') {
            $query->where('stock', '>', 5);
        }

        // Search
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Category filter
        if ($category = $request->input('category')) {
            $query->where('category_id', $category);
        }

        $products = $query->orderBy('stock')->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        // Stats
        $stats = [
            'total'      => Product::count(),
            'in_stock'   => Product::where('stock', '>', 5)->count(),
            'low_stock'  => Product::whereBetween('stock', [1, 5])->count(),
            'out_stock'  => Product::where('stock', '<=', 0)->count(),
            'total_value' => Product::sum(\DB::raw('stock * price')),
        ];

        return view('inventory.index', compact('products', 'categories', 'stats', 'filter'));
    }
}
