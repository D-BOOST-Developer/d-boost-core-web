<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Low stock filter
        if ($request->filled('low_stock')) {
            $query->lowStock();
        }

        $products = $query->orderBy('name')->paginate(15);
        $categories = Product::distinct()->pluck('category')->filter();

        return view('inventory.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Product::distinct()->pluck('category')->filter();
        return view('inventory.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_published_to_storefront' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            // Validate file
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Store new image with proper validation
            try {
                $validated['image_path'] = $request->file('image')->store('products', 'public');

                // Verify file was stored successfully
                if (!$validated['image_path']) {
                    return back()->withErrors(['image' => 'Failed to upload image. Please try again.'])->withInput();
                }
            } catch (\Exception $e) {
                \Log::error('Product image upload failed: ' . $e->getMessage());
                return back()->withErrors(['image' => 'Failed to upload image. Please try again.'])->withInput();
            }
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_published_to_storefront'] = $request->has('is_published_to_storefront');

        Product::create($validated);

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['salesDetails.sale', 'creator', 'updater']);
        
        // Get sales statistics
        $totalSold = $product->salesDetails->sum('quantity');
        $totalRevenue = $product->salesDetails->sum('line_total');
        $avgOrderQuantity = $totalSold > 0 ? $product->salesDetails->avg('quantity') : 0;

        return view('inventory.products.show', compact('product', 'totalSold', 'totalRevenue', 'avgOrderQuantity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Product::distinct()->pluck('category')->filter();
        return view('inventory.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_published_to_storefront' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            // Validate file
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Delete old image if exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            // Store new image with proper validation
            try {
                $validated['image_path'] = $request->file('image')->store('products', 'public');

                // Verify file was stored successfully
                if (!$validated['image_path']) {
                    return back()->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }
            } catch (\Exception $e) {
                \Log::error('Product image upload failed: ' . $e->getMessage());
                return back()->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        } elseif ($request->boolean('remove_image')) {
            // Remove image if requested
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = null;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_published_to_storefront'] = $request->has('is_published_to_storefront');

        $product->update($validated);

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle product status
     */
    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Product {$status} successfully.");
    }

    /**
     * Toggle whether the product is published to the public storefront.
     */
    public function togglePublishedToStorefront(Product $product)
    {
        $product->update([
            'is_published_to_storefront' => !$product->is_published_to_storefront,
        ]);

        $state = $product->is_published_to_storefront ? 'published to' : 'removed from';
        return back()->with('success', "Product {$state} the storefront.");
    }
}

