<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Services\BrowsingHistoryService;


class ProductController extends Controller
{

    public function __construct(
        protected BrowsingHistoryService $history
    ) {}


    public function index(Request $request)
    {
        // $products = Product::with('category')->latest()->paginate(10);
        // return view('products.index', compact('products'));

        $user = auth()->user();

        // -------------------------
        //  USER → beautiful grid
        // -------------------------
        if ($user->isUser()) {
            $query = Product::with('category')->where('status', 'active');

            // Optional search
            if ($search = $request->input('search')) {
                $query->where('name', 'like', "%{$search}%");
            }

            // Optional category filter
            if ($categoryId = $request->input('category')) {
                $query->where('category_id', $categoryId);
            }

            // Sort
            $sort = $request->input('sort', 'latest');
            match ($sort) {
                'price_asc'  => $query->orderBy('price'),
                'price_desc' => $query->orderByDesc('price'),
                'name'       => $query->orderBy('name'),
                default      => $query->latest(),
            };

            $products   = $query->paginate(12)->withQueryString();
            $categories = Category::withCount('products')->orderBy('name')->get();

            return view('products.user-index', compact('products', 'categories'));
        }

        // -------------------------
        //  ADMIN / MANAGER → admin table
        // -------------------------
        $products = Product::with('category')->latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        // $categories = Category::all();
        // return view('products.create', compact('categories'));

        $categories = \App\Models\Category::orderBy('name')->get();

        // Map every category_id => spec-key used in config/product_specs.php
        $categorySpecMap = $categories->mapWithKeys(fn($c) => [
            $c->id => $this->resolveSpecKey($c->name),
        ])->toArray();

        // Load the entire config so Alpine can switch templates client-side
        $specSections = config('product_specs');

        return view('products.create', compact(
            'categories',
            'categorySpecMap',
            'specSections',
        ));
    }

    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'name'        => 'required|string|max:255',
        //     'description' => 'nullable|string',
        //     'price'       => 'required|numeric|min:0',
        //     'stock'       => 'required|integer|min:0',
        //     'category_id' => 'required|exists:categories,id',
        // ]);
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock'          => 'required|integer|min:0',
            'description'    => 'nullable|string|max:5000',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'         => 'required|in:active,draft,archived',
            'featured'       => 'nullable|boolean',
            'tags'           => 'nullable|string|max:500',

            'specs'                     => 'nullable|array',
            'specs.*'                   => 'nullable|array',
            'specs.*.*.label'           => 'nullable|string|max:255',
            'specs.*.*.value'           => 'nullable|string|max:2000',
        ]);


        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle tags (comma-separated string → array)
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
        } else {
            $validated['tags'] = [];
        }

        // featured checkbox
        $validated['featured'] = $request->boolean('featured');



        $product =  Product::create($validated);

        $this->syncSpecifications($product, $request->input('specs', []));


        return redirect()->route('products.index', $product)
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        // $categories = Category::all();
        // $existingSpecs = $product->specificationsBySection();

        // return view('products.edit', compact('product', 'categories', 'existingSpecs'));

        $categories = \App\Models\Category::orderBy('name')->get();

        $categorySpecMap = $categories->mapWithKeys(fn($c) => [
            $c->id => $this->resolveSpecKey($c->name),
        ])->toArray();

        $specSections  = config('product_specs');
        $existingSpecs = $product->specificationsBySection();

        return view('products.edit', compact(
            'product',
            'categories',
            'categorySpecMap',
            'specSections',
            'existingSpecs',
        ));
    }

    public function update(Request $request, Product $product)
    {
        // $validated = $request->validate([
        //     'name'        => 'required|string|max:255',
        //     'description' => 'nullable|string',
        //     'price'       => 'required|numeric|min:0',
        //     'stock'       => 'required|integer|min:0',
        //     'category_id' => 'required|exists:categories,id',
        // ]);

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock'          => 'required|integer|min:0',
            'description'    => 'nullable|string|max:5000',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'         => 'required|in:active,draft,archived',
            'featured'       => 'nullable|boolean',
            'tags'           => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        if (!empty($validated['tags'])) {
            $validated['tags'] = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
        } else {
            $validated['tags'] = [];
        }

        $validated['featured'] = $request->boolean('featured');

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // $product->delete();
        // return redirect()->route('products.index')
        //     ->with('success', 'Product deleted successfully.');
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function show(Product $product)
    {

        $product->load(['specifications', 'category', 'approvedReviews.user']);

        $specsBySection = $product->specificationsBySection();


        $this->history->record($product);

        $recentlyViewed = $this->history->recentlyViewed(excludeProductId: $product->id, limit: 12);
        $user = auth()->user();
        if ($user && $user->isUser()) {
            $product->load(['approvedReviews.user' => fn($query) => $query->latest()->limit(20),]);
            $userReview = Review::where('product_id', $product->id)->where('user_id', $user->id)->first();
            $canReview = !$userReview;
            return view('products.user-show', compact('product',  'specsBySection', 'userReview', 'canReview', 'recentlyViewed'));
        }
        if ($user && $user->hasRole('admin', 'manager')) {
            return redirect()->route('products.edit', $product);
        }
        return view('products.show', compact('product', 'specsBySection', 'recentlyViewed'));
    }

    public function resolveSpecKey(string $name): string
    {
        // $key = strtolower(trim($name));
        // return array_key_exists($key, config('product_specs')) ? $key : 'default';
        $key = strtolower(trim($name));

        // Try exact match
        if (array_key_exists($key, config('product_specs'))) {
            return $key;
        }

        // Try singular form ("laptops" → "laptop")
        $singular = \Illuminate\Support\Str::singular($key);
        if (array_key_exists($singular, config('product_specs'))) {
            return $singular;
        }

        // Try matching by starts-with (e.g. "Gaming Laptop" → "laptop")
        foreach (array_keys(config('product_specs')) as $candidate) {
            if ($candidate === 'default') continue;
            if (str_contains($key, $candidate) || str_contains($candidate, $key)) {
                return $candidate;
            }
        }

        return 'default';
    }

    protected function syncSpecifications(Product $product, array $specs): void
    {
        $product->specifications()->delete();

        $position = 0;
        foreach ($specs as $section => $rows) {
            if (! is_array($rows)) continue;
            foreach ($rows as $row) {
                $label = trim($row['label'] ?? '');
                $value = trim($row['value'] ?? '');
                if ($label === '') continue;

                $product->specifications()->create([
                    'section'  => $section,
                    'label'    => $label,
                    'value'    => $value,
                    'position' => $position++,
                ]);
            }
        }
    }
}
