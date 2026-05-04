<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('sku', 'ilike', "%{$search}%")
                  ->orWhere('category', 'ilike', "%{$search}%");
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
        if ($request->filled('low_stock') && $request->boolean('low_stock')) {
            $query->lowStock();
        }

        // Out of stock filter
        if ($request->filled('out_of_stock') && $request->boolean('out_of_stock')) {
            $query->where('stock_quantity', '<=', 0);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->orderBy('name')->paginate($perPage);

        // Get total counts for filter chips (without current filters)
        $totalLowStock = Product::lowStock()->count();
        $totalOutOfStock = Product::where('stock_quantity', '<=', 0)->count();

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'categories' => Product::distinct()->pluck('category')->filter()->values(),
            'filter_counts' => [
                'low_stock' => $totalLowStock,
                'out_of_stock' => $totalOutOfStock,
            ],
        ]);
    }

    /**
     * Store a newly created product
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
            'max_stock_level' => 'nullable|integer|min:0',
            'category' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            try {
                $validated['image_path'] = $request->file('image')->store('products', 'public');
            } catch (\Exception $e) {
                \Log::error('Product image upload failed: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Failed to upload image',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product->fresh()
        ], 201);
    }

    /**
     * Display the specified product
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        // Get sales statistics
        $totalSold = $product->salesDetails()->sum('quantity');
        $totalRevenue = $product->salesDetails()->sum('line_total');
        $avgOrderQuantity = $product->salesDetails()->count() > 0 
            ? $product->salesDetails()->avg('quantity') 
            : 0;

        return response()->json([
            'data' => $product,
            'statistics' => [
                'total_sold' => (int) $totalSold,
                'total_revenue' => (float) $totalRevenue,
                'avg_order_quantity' => (float) $avgOrderQuantity,
            ]
        ]);
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'sometimes|required|string|max:255|unique:products,sku,' . $product->id,
            'price' => 'sometimes|required|numeric|min:0',
            'cost' => 'sometimes|required|numeric|min:0',
            'min_stock_level' => 'sometimes|required|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'category' => 'sometimes|required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            try {
                $validated['image_path'] = $request->file('image')->store('products', 'public');
            } catch (\Exception $e) {
                \Log::error('Product image upload failed: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Failed to upload image',
                    'error' => $e->getMessage()
                ], 500);
            }
        } elseif ($request->boolean('remove_image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = null;
        }

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product->fresh()
        ]);
    }

    /**
     * Remove the specified product
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Delete image if exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Toggle product active status
     */
    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->is_active = !$product->is_active;
        $product->save();

        return response()->json([
            'message' => 'Product status updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Adjust stock quantity
     */
    public function adjustStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:in,out',
            'notes' => 'nullable|string',
        ]);

        $quantity = abs($validated['quantity']);
        
        if ($validated['type'] === 'out' && $product->stock_quantity < $quantity) {
            return response()->json([
                'message' => 'Insufficient stock',
                'error' => "Available stock: {$product->stock_quantity}, requested: {$quantity}"
            ], 400);
        }

        DB::transaction(function () use ($product, $validated, $quantity) {
            $stockBefore = $product->stock_quantity;
            
            if ($validated['type'] === 'in') {
                $product->increaseStock($quantity);
                $transactionType = 'adjustment_increase';
            } else {
                $product->reduceStock($quantity);
                $transactionType = 'adjustment_decrease';
            }

            // Create inventory transaction record
            $product->inventoryTransactions()->create([
                'user_id' => auth()->id(),
                'transaction_type' => $transactionType,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $product->stock_quantity,
                'reference_type' => 'manual',
                'status' => 'auto_approved',
                'reason' => $validated['notes'] ?? 'manual_adjustment',
                'notes' => $validated['notes'] ?? null,
                'transaction_date' => now(),
                'created_by' => auth()->id(),
            ]);
        });

        return response()->json([
            'message' => 'Stock adjusted successfully',
            'data' => $product->fresh()
        ]);
    }

    /**
     * Get product categories
     */
    public function categories()
    {
        $categories = Product::distinct()->pluck('category')->filter()->values();
        
        return response()->json([
            'data' => $categories
        ]);
    }

    /**
     * Get inventory dashboard statistics
     */
    public function dashboard(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $totalProducts = Product::count();
        $activeProducts = Product::active()->count();
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::where('stock_quantity', '<=', 0)->count();
        
        $totalStockValue = Product::sum(DB::raw('stock_quantity * cost'));
        $totalStockQuantity = Product::sum('stock_quantity');
        $avgStockValuePerProduct = $totalProducts > 0 ? $totalStockValue / $totalProducts : 0;

        // Stock movements (from inventory transactions)
        $stockIn = \App\Models\InventoryTransaction::whereIn('transaction_type', [
                'purchase',
                'return_from_customer',
                'adjustment_increase',
                'transfer_in',
                'initial_stock'
            ])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'auto_approved'])
            ->sum('quantity');
        
        $stockOut = \App\Models\InventoryTransaction::whereIn('transaction_type', [
                'sale',
                'return_to_supplier',
                'adjustment_decrease',
                'transfer_out',
                'damaged',
                'expired'
            ])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'auto_approved'])
            ->sum('quantity');
        
        $netStockMovement = $stockIn - $stockOut;

        return response()->json([
            'total_stock_value' => (float) $totalStockValue,
            'total_stock_quantity' => (int) $totalStockQuantity,
            'avg_stock_value_per_product' => (float) $avgStockValuePerProduct,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'stock_movements' => [
                'in' => (int) $stockIn,
                'out' => (int) $stockOut,
                'net' => (int) $netStockMovement,
            ],
        ]);
    }
}

