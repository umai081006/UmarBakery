<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index(): View
    {
        $categories = Category::where('is_active', true)->get();
        
        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        // Wishlist IDs (single query, no N+1)
        $wishlistIds = [];
        if (auth()->check()) {
            $wishlistIds = auth()->user()->wishlists()->pluck('product_id')->toArray();
        }

        return view('public.home', compact('categories', 'featuredProducts', 'wishlistIds'));
    }
}
