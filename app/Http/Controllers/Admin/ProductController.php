<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\CloudinaryService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku|max:50',
            'description' => 'required|string',
            'composition' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        try {
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $imageUrl = $this->cloudinaryService->upload($request->file('image'), 'products');
            }

            $product = Product::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . time(),
                'sku' => $request->sku ? strtoupper($request->sku) : uniqid('TEMP_'),
                'description' => $request->description,
                'composition' => $request->composition,
                'price' => $request->price,
                'stock' => $request->stock,
                'weight' => $request->weight,
                'image_url' => $imageUrl,
                'is_active' => $request->has('is_active'),
            ]);

            if (empty($request->sku)) {
                $product->update([
                    'sku' => 'UB-PRD-' . str_pad($product->id, 6, '0', STR_PAD_LEFT)
                ]);
            }

            return redirect()->route('admin.products.index')->with('success', 'Roti berhasil ditambahkan!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'description' => 'required|string',
            'composition' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        try {
            $imageUrl = $product->image_url;
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image_url) {
                    $this->cloudinaryService->delete($product->image_url);
                }
                $imageUrl = $this->cloudinaryService->upload($request->file('image'), 'products');
            }

            $product->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . time(),
                'sku' => strtoupper($request->sku),
                'description' => $request->description,
                'composition' => $request->composition,
                'price' => $request->price,
                'stock' => $request->stock,
                'weight' => $request->weight,
                'image_url' => $imageUrl,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.products.index')->with('success', 'Roti berhasil diperbarui!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        try {
            if ($product->image_url) {
                $this->cloudinaryService->delete($product->image_url);
            }
            $product->delete();

            return redirect()->route('admin.products.index')->with('success', 'Roti berhasil dihapus!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}
