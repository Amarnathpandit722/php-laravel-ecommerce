<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    // protected $fillable = ['name', 'description', 'price', 'stock', 'category_id'];

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'image',
        'price',
        'discount_price',
        'stock',
        'category_id',
        'status',
        'featured',
        'tags',
    ];

    protected $casts = [
        'tags'      => 'array',
        'featured'  => 'boolean',
        'price'     => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(5);
            }
            if (empty($product->sku)) {
                $product->sku = 'PRD-' . strtoupper(Str::random(8));
            }
        });
    }

    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price);
    }

    // Accessor: image URL with fallback
    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff&size=200';
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }


    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    // Stats helpers
    public function getAverageRatingAttribute(): float
    {
        return round((float) $this->approvedReviews()->avg('rating'), 2);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    // Star breakdown: [1=>count, 2=>..., 5=>...]
    public function getRatingBreakdownAttribute(): array
    {
        $rows = $this->approvedReviews()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $out = [];
        for ($i = 1; $i <= 5; $i++) $out[$i] = (int) ($rows[$i] ?? 0);
        return $out;
    }

    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('position');
    }

    /**
     * Returns specs grouped by section, e.g.
     * ['Processor' => [['label' => 'Brand', 'value' => 'Intel'], ...], ...]
     */
    public function specificationsBySection(): array
    {
        return $this->specifications
            ->groupBy('section')
            ->map(fn($rows) => $rows->map(fn($r) => [
                'label' => $r->label,
                'value' => $r->value,
            ])->values()->all())
            ->toArray();
    }
}
