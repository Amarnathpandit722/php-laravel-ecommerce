<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    // -------------------------------------------------------------
    //  STORE — create a new review (any authenticated user)
    // -------------------------------------------------------------
    public function store(Request $request, Product $product)
    {
        // Rule: only USER role can post reviews (admins/moderators don't)
        // If you want admins to review too, remove this check.
        if (!auth()->user()->isUser()) {
            abort(403, 'Admins and managers cannot post reviews.');
        }

        // One review per product per user
        if (Review::where('product_id', $product->id)->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You already reviewed this product.');
        }

        $validated = $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'title'    => 'nullable|string|max:120',
            'body'     => 'required|string|min:10|max:2000',
            'images'   => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        // Upload images
        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('reviews', 'public');
            }
        }

        Review::create([
            'product_id'  => $product->id,
            'user_id'     => auth()->id(),
            'rating'      => $validated['rating'],
            'title'       => $validated['title'] ?? null,
            'body'        => $validated['body'],
            'images'      => $paths,
            'status'      => 'pending', // awaits admin approval
        ]);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Thanks! Your review is pending approval.');
    }

    // -------------------------------------------------------------
    //  UPDATE — author edits their own pending/rejected review
    // -------------------------------------------------------------
    public function update(Request $request, Review $review)
    {
        // Author can edit their own review
        // Admin can edit any review
        if (!auth()->user()->isAdmin() && !$review->isOwnedBy(auth()->user())) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:120',
            'body'   => 'required|string|min:10|max:2000',
        ]);

        // If the author edited, reset status back to pending for re-approval
        // (admin edits skip this)
        if (!$review->isOwnedBy(auth()->user())) {
            // Admin editing — keep status unless explicitly provided
        } else {
            $validated['status'] = 'pending';
        }

        $review->update($validated);

        return back()->with('success', 'Review updated.');
    }

    // -------------------------------------------------------------
    //  DESTROY — author can delete own; admin can delete any
    // -------------------------------------------------------------
    public function destroy(Review $review)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && !$review->isOwnedBy($user)) {
            abort(403);
        }

        // Delete images
        foreach ($review->images ?? [] as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $review->delete();

        return back()->with('success', 'Review deleted.');
    }

    // -------------------------------------------------------------
    //  MARK HELPFUL — any authenticated user (except the author)
    // -------------------------------------------------------------
    public function markHelpful(Review $review)
    {
        if ($review->isOwnedBy(auth()->user())) {
            return back()->with('error', 'You cannot mark your own review as helpful.');
        }

        $review->increment('helpful_count');
        return back();
    }

    // =============================================================
    //  ADMIN-ONLY moderation endpoints
    // =============================================================

    /**
     * List reviews for moderation (admin + manager)
     */

    public function index(Request $request)
    {
        if (!auth()->user()->hasRole('admin', 'manager')) {
            abort(403);
        }

        $query = Review::with(['product', 'user'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        $stats = [
            'total'    => Review::count(),
            'pending'  => Review::where('status', 'pending')->count(),
            'approved' => Review::where('status', 'approved')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
        ];

        return view('reviews.index', compact('reviews', 'stats'));
    }


    /**
     * Approve a review (admin + manager)
     */
    public function approve(Review $review)
    {
        if (!auth()->user()->hasRole('admin', 'manager')) {
            abort(403);
        }

        $review->update(['status' => 'approved']);

        return back()->with('success', 'Review approved.');
    }

    /**
     * Reject a review (admin + manager)
     */
    public function reject(Review $review)
    {
        if (!auth()->user()->hasRole('admin', 'manager')) {
            abort(403);
        }

        $review->update(['status' => 'rejected']);

        return back()->with('success', 'Review rejected.');
    }

    // ReviewController
    public function mine()
    {
        $reviews = Review::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('reviews.mine', compact('reviews'));
    }
}
