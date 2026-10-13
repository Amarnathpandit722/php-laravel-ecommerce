<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BrowsingHistoryService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Record a product view for the current user/session.
     */
    public function record(Product $product): void
    {
        $userId    = auth()->id();
        $sessionId = session()->getId();

        // Avoid duplicate row for the same product — just bump the timestamp
        ProductView::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id'    => $userId,
                'session_id' => $userId ? null : $sessionId,
            ],
            ['viewed_at' => now()]
        );

        // Keep only last 50 per user/session
        $this->prune($userId, $sessionId);
    }

    /**
     * Get the recently viewed products (excluding current one).
     */
    public function recentlyViewed(?int $excludeProductId = null, int $limit = 12): Collection
    {
        $query = ProductView::query()
            ->with('product.category')
            ->whereHas('product') // skip deleted products
            ->when(auth()->check(), fn($q) => $q->where('user_id', auth()->id()))
            ->when(auth()->guest(), fn($q) => $q->where('session_id', session()->getId()))
            ->when($excludeProductId, fn($q) => $q->where('product_id', '!=', $excludeProductId))
            ->orderByDesc('viewed_at')
            ->limit($limit);

        return $query->get()->pluck('product')->filter()->values();
    }

    /**
     * Trim old entries so the table doesn't grow forever.
     */
    protected function prune(?int $userId, string $sessionId): void
    {
        $keep = 50;

        $query = ProductView::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn($q) => $q->where('session_id', $sessionId))
            ->orderByDesc('viewed_at');

        $idsToDelete = $query->skip($keep)->take(PHP_INT_MAX)->pluck('id');

        if ($idsToDelete->isNotEmpty()) {
            ProductView::whereIn('id', $idsToDelete)->delete();
        }
    }
}
