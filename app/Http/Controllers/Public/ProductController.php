<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display product catalog with search, filter, and sorting.
     */
    public function index(Request $request): View
    {
        $query = Product::with('category')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('is_active', true);

        // 1. Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('sku', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        // 2. Filter by Category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // 3. Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        // Wishlist IDs (single query, no N+1)
        $wishlistIds = [];
        if (auth()->check()) {
            $wishlistIds = auth()->user()->wishlists()->pluck('product_id')->toArray();
        }

        return view('public.products.index', compact('products', 'categories', 'wishlistIds'));
    }

    /**
     * Display the specified product detail.
     */
    public function show(string $slug): View
    {
        $product = Product::with(['category', 'reviews.user'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Fetch related products (same category, excluding current product)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->take(4)
            ->get();

        // Wishlist IDs (single query, no N+1)
        $wishlistIds = [];
        if (auth()->check()) {
            $wishlistIds = auth()->user()->wishlists()->pluck('product_id')->toArray();
        }

        return view('public.products.show', compact('product', 'relatedProducts', 'wishlistIds'));
    }
}
