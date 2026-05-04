<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\StorefrontConfig;
use App\Models\UserBusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StorefrontOrderController extends Controller
{
    private function resolveConfig(string $slug): StorefrontConfig
    {
        return StorefrontConfig::where('slug', $slug)
            ->published()
            ->firstOrFail();
    }

    private function cartKey(string $slug): string
    {
        return "storefront_cart.{$slug}";
    }

    private function findMerchantProduct(StorefrontConfig $config, string $productId): ?Product
    {
        return Product::withoutGlobalScope('user')
            ->where('id', $productId)
            ->where('user_id', $config->user_id)
            ->first();
    }

    public function addToCart(Request $request, string $slug)
    {
        $config = $this->resolveConfig($slug);

        $validated = $request->validate([
            'product_id' => 'required|string',
            'quantity' => 'nullable|integer|min:1|max:999',
        ]);

        $product = $this->findMerchantProduct($config, $validated['product_id']);

        if (!$product || !$product->is_published_to_storefront || !$product->is_active) {
            return back()->with('error', 'Produk tidak tersedia.');
        }

        $qty = $validated['quantity'] ?? 1;
        $cart = session($this->cartKey($slug), []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty'] += $qty;
        } else {
            $cart[$product->id] = [
                'qty' => $qty,
                'price_snapshot' => (float) $product->price,
                'name_snapshot' => $product->name,
            ];
        }

        session([$this->cartKey($slug) => $cart]);

        return redirect()->route('public.store.cart', $slug)
            ->with('success', "{$product->name} ditambahkan ke keranjang.");
    }

    public function updateCart(Request $request, string $slug, string $productId)
    {
        $this->resolveConfig($slug);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $cart = session($this->cartKey($slug), []);

        if (!isset($cart[$productId])) {
            return back()->with('error', 'Item tidak ada di keranjang.');
        }

        $cart[$productId]['qty'] = $validated['quantity'];
        session([$this->cartKey($slug) => $cart]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function removeFromCart(string $slug, string $productId)
    {
        $this->resolveConfig($slug);

        $cart = session($this->cartKey($slug), []);
        unset($cart[$productId]);
        session([$this->cartKey($slug) => $cart]);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function viewCart(string $slug)
    {
        $config = $this->resolveConfig($slug);
        $cart = session($this->cartKey($slug), []);

        $items = $this->loadCartItems($config, $cart);
        $totals = $this->calculateTotals($items);

        return view('storefront.public.cart', [
            'config' => $config,
            'items' => $items,
            'totals' => $totals,
        ]);
    }

    public function checkoutForm(string $slug)
    {
        $config = $this->resolveConfig($slug);
        $cart   = session($this->cartKey($slug), []);

        if (empty($cart)) {
            return redirect()->route('public.store.cart', $slug)
                ->with('error', 'Keranjang Anda kosong.');
        }

        $items           = $this->loadCartItems($config, $cart);
        $totals          = $this->calculateTotals($items);
        $businessProfile = UserBusinessProfile::where('user_id', $config->user_id)->first();

        return view('storefront.public.checkout', [
            'config'          => $config,
            'items'           => $items,
            'totals'          => $totals,
            'businessProfile' => $businessProfile,
        ]);
    }

    public function placeOrder(Request $request, string $slug)
    {
        $config = $this->resolveConfig($slug);
        $cart = session($this->cartKey($slug), []);

        if (empty($cart)) {
            return redirect()->route('public.store.cart', $slug)
                ->with('error', 'Keranjang Anda kosong.');
        }

        $validated = $request->validate([
            'customer_name'    => 'required|string|max:100',
            'customer_phone'   => 'required|string|max:30',
            'customer_address' => 'required|string|max:1000',
            'payment_method'   => 'nullable|string|in:cash,credit_card,debit_card,bank_transfer,e_wallet,qris',
            'notes'            => 'nullable|string|max:500',
        ]);

        $items = $this->loadCartItems($config, $cart);

        if (empty($items)) {
            return redirect()->route('public.store.catalog', $slug)
                ->with('error', 'Produk di keranjang tidak lagi tersedia.');
        }

        $totals = $this->calculateTotals($items);

        $sale = DB::transaction(function () use ($config, $items, $totals, $validated) {
            $sale = Sale::create([
                'user_id' => $config->user_id,
                'customer_id' => null,
                'sale_date' => now()->toDateString(),
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax'],
                'discount_amount' => 0,
                'total_amount' => $totals['total'],
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'status' => 'pending',
                'order_source' => 'storefront',
                'notes' => $validated['notes'] ?? null,
                'customer_snapshot' => [
                    'name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'],
                    'address' => $validated['customer_address'],
                    'notes' => $validated['notes'] ?? null,
                ],
            ]);

            foreach ($items as $item) {
                SalesDetail::create([
                    'user_id' => $config->user_id,
                    'sale_id' => $sale->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'discount_percentage' => 0,
                    'discount_amount' => 0,
                    'line_total' => $item['line_total'],
                ]);
            }

            return $sale;
        });

        session()->forget($this->cartKey($slug));
        Cache::forget('pending_storefront_count_' . $config->user_id);
        
        $historyKey = "storefront_orders_{$slug}";
        $history = session()->get($historyKey, []);
        $history[] = $sale->id;
        session()->put($historyKey, array_unique($history));

        return redirect()->route('public.store.order-success', [
            'slug' => $slug,
            'invoice' => $sale->invoice_number,
        ]);
    }

    public function orderSuccess(string $slug, string $invoice)
    {
        $config = $this->resolveConfig($slug);

        $sale = Sale::withoutGlobalScope('user')
            ->where('user_id', $config->user_id)
            ->where('invoice_number', $invoice)
            ->where('order_source', 'storefront')
            ->with('salesDetails.product')
            ->firstOrFail();

        $businessProfile = UserBusinessProfile::where('user_id', $config->user_id)->first();

        return view('storefront.public.order-success', [
            'config'          => $config,
            'sale'            => $sale,
            'businessProfile' => $businessProfile,
        ]);
    }

    public function myOrders(string $slug)
    {
        $config = $this->resolveConfig($slug);

        $historyKey = "storefront_orders_{$slug}";
        $orderIds   = session()->get($historyKey, []);

        $orders = collect();
        if (!empty($orderIds)) {
            $orders = Sale::withoutGlobalScope('user')
                ->where('user_id', $config->user_id)
                ->whereIn('id', $orderIds)
                ->where('order_source', 'storefront')
                ->with('salesDetails.product')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $businessProfile = UserBusinessProfile::where('user_id', $config->user_id)->first();

        return view('storefront.public.my-orders', [
            'config'          => $config,
            'orders'          => $orders,
            'slug'            => $slug,
            'businessProfile' => $businessProfile,
        ]);
    }

    private function loadCartItems(StorefrontConfig $config, array $cart): array
    {
        $items = [];

        foreach ($cart as $productId => $line) {
            $product = $this->findMerchantProduct($config, $productId);

            if (!$product || !$product->is_active || !$product->is_published_to_storefront) {
                continue;
            }

            $unitPrice = (float) $product->price;
            $qty = (int) $line['qty'];
            $lineTotal = $unitPrice * $qty;

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ];
        }

        return $items;
    }

    private function calculateTotals(array $items): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['line_total'];
        }

        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ];
    }
}
