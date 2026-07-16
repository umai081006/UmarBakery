<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CloudinaryService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $categories = Category::withCount('products')->latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        try {
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $imageUrl = $this->cloudinaryService->upload($request->file('image'), 'categories');
            }

            Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'image' => $imageUrl,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(int $id): View
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        try {
            $imageUrl = $category->image;
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image) {
                    $this->cloudinaryService->delete($category->image);
                }
                $imageUrl = $this->cloudinaryService->upload($request->file('image'), 'categories');
            }

            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'image' => $imageUrl,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        try {
            if ($category->image) {
                $this->cloudinaryService->delete($category->image);
            }
            $category->delete();

            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}
