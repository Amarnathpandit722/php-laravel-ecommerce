<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'body',
        'images',
        'status',
        'is_verified_purchase',
        'helpful_count',
    ];

    protected $casts = [
        'images'              => 'array',
        'is_verified_purchase' => 'boolean',
        'rating'              => 'integer',
        'helpful_count'       => 'integer',
    ];

    // ---------- Relationships ----------
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ---------- Scopes ----------
    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }
    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    // ---------- Accessors ----------
    public function getImageUrlsAttribute(): array
    {
        if (empty($this->images)) return [];
        return array_map(fn($p) => asset('storage/' . $p), $this->images);
    }

    // ---------- Helpers ----------
    public function isOwnedBy(?User $user): bool
    {
        return $user && $user->id === $this->user_id;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
