<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist.
     */
    public function index(Request $request): View
    {
        $wishlists = $request->user()->wishlists()->with('product.category')->latest()->get();
        return view('customer.wishlists.index', compact('wishlists'));
    }

    /**
     * Toggle a product in wishlist.
     */
    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();
        
        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return redirect()->back()->with('success', 'Roti dihapus dari wishlist.');
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            return redirect()->back()->with('success', 'Roti ditambahkan ke wishlist!');
        }
    }
}
