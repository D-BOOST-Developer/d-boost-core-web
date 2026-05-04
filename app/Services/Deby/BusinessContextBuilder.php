<?php

namespace App\Services\Deby;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Build a per-user business snapshot to inject into Deby AI's system prompt.
 *
 * Strict user isolation: queries rely on BelongsToUser global scope (active
 * because controller authenticates the user before invoking this builder).
 * No raw queries bypass scopes.
 */
class BusinessContextBuilder
{
    private User $user;

    public function __construct(User $user)
    {
        if (!$user || empty($user->id)) {
            throw new \InvalidArgumentException('BusinessContextBuilder requires an authenticated user.');
        }
        $this->user = $user;
    }

    public function build(bool $useCache = true): string
    {
        $key = "deby_ctx_{$this->user->id}";

        if (!$useCache) {
            return $this->compose();
        }

        return Cache::remember($key, 300, fn () => $this->compose());
    }

    public static function invalidate(string $userId): void
    {
        Cache::forget("deby_ctx_{$userId}");
    }

    private function compose(): string
    {
        $sections = [
            $this->profileSection(),
            $this->storefrontSection(),
            $this->inventorySection(),
            $this->financeTodaySection(),
            $this->financeMonthSection(),
            $this->topPerformersSection(),
            $this->trendSection(),
        ];

        return implode("\n\n", array_filter($sections));
    }

    private function profileSection(): string
    {
        $profile = $this->user->businessProfile;

        $lines = ['=== PROFIL BISNIS ==='];
        if (!$profile) {
            $lines[] = '- (Profil bisnis belum diisi. Sarankan user melengkapi onboarding.)';
            return implode("\n", $lines);
        }

        $lines[] = '- Nama Bisnis: ' . ($profile->business_name ?: '-');
        $lines[] = '- Kategori: ' . ($profile->business_category ?: '-');
        $lines[] = '- Pemilik: ' . ($profile->owner_name ?: $this->user->name);
        if ($profile->business_age_month) {
            $lines[] = '- Umur Bisnis: ' . $profile->business_age_month . ' bulan';
        }
        if ($profile->monthly_revenue_range) {
            $lines[] = '- Range Omzet Bulanan: ' . $profile->monthly_revenue_range;
        }
        if ($profile->business_address) {
            $lines[] = '- Alamat: ' . $profile->business_address;
        }
        if ($profile->contact_phone) {
            $lines[] = '- Kontak: ' . $profile->contact_phone;
        }

        return implode("\n", $lines);
    }

    private function storefrontSection(): string
    {
        $storefront = $this->user->storefrontConfig;

        $lines = ['=== DIGITAL STOREFRONT ==='];
        if (!$storefront) {
            $lines[] = '- Belum punya storefront aktif.';
            return implode("\n", $lines);
        }

        $publicUrl = url('/store/' . $storefront->slug);
        $status = $storefront->is_published ? 'Aktif (published)' : 'Draft (belum dipublish)';

        $publishedProducts = Product::where('is_published_to_storefront', true)
            ->where('is_active', true)
            ->count();

        $marketplaceLinks = $this->user->marketplaceLinks()->where('is_active', true)->get();
        $totalClicks = (int) $marketplaceLinks->sum('click_count');

        $lines[] = '- URL Publik: ' . $publicUrl;
        $lines[] = '- Status: ' . $status;
        $lines[] = '- Judul Storefront: ' . ($storefront->storefront_title ?: '-');
        $lines[] = '- Produk Dipublish: ' . $publishedProducts;
        $lines[] = '- Marketplace Links Aktif: ' . $marketplaceLinks->count() . ' (total click: ' . $totalClicks . ')';

        return implode("\n", $lines);
    }

    private function inventorySection(): string
    {
        $totalProducts = Product::count();

        $lines = ['=== INVENTORY SNAPSHOT ==='];
        if ($totalProducts === 0) {
            $lines[] = '- Belum ada produk dalam inventory.';
            return implode("\n", $lines);
        }

        $lowStock = Product::lowStock()->orderBy('stock_quantity')->limit(5)->get(['name', 'stock_quantity', 'min_stock_level']);
        $totalInventoryValue = (float) Product::sum(DB::raw('cost * stock_quantity'));

        $topCategory = Product::select('category', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('cnt')
            ->first();

        $lines[] = '- Total Produk: ' . $totalProducts;
        $lines[] = '- Total Nilai Inventory (cost × stok): Rp ' . number_format($totalInventoryValue, 0, ',', '.');

        if ($lowStock->isEmpty()) {
            $lines[] = '- Stok Rendah: tidak ada (semua aman).';
        } else {
            $lines[] = '- Stok Rendah: ' . $lowStock->count() . ' produk';
            foreach ($lowStock as $p) {
                $lines[] = '  • ' . $p->name . ' — sisa ' . $p->stock_quantity . ' (min ' . $p->min_stock_level . ')';
            }
        }

        if ($topCategory) {
            $lines[] = '- Kategori Terbanyak: ' . $topCategory->category . ' (' . $topCategory->cnt . ' produk)';
        }

        return implode("\n", $lines);
    }

    private function financeTodaySection(): string
    {
        $today = Carbon::today();

        $revenue = (float) Sale::whereDate('sale_date', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $expense = (float) Expense::whereDate('expense_date', $today)->sum('amount');
        $txCount = Sale::whereDate('sale_date', $today)
            ->where('status', '!=', 'cancelled')
            ->count();

        $lines = [
            '=== KEUANGAN HARI INI ===',
            '- Pendapatan: Rp ' . number_format($revenue, 0, ',', '.'),
            '- Pengeluaran: Rp ' . number_format($expense, 0, ',', '.'),
            '- Profit: Rp ' . number_format($revenue - $expense, 0, ',', '.'),
            '- Jumlah Transaksi: ' . $txCount,
        ];

        return implode("\n", $lines);
    }

    private function financeMonthSection(): string
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $revenue = (float) Sale::whereBetween('sale_date', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $expense = (float) Expense::whereBetween('expense_date', [$start, $end])->sum('amount');
        $txCount = Sale::whereBetween('sale_date', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->count();

        $aov = $txCount > 0 ? $revenue / $txCount : 0.0;

        $lines = [
            '=== KEUANGAN BULAN INI (' . $start->format('M Y') . ') ===',
            '- Total Pendapatan: Rp ' . number_format($revenue, 0, ',', '.'),
            '- Total Pengeluaran: Rp ' . number_format($expense, 0, ',', '.'),
            '- Profit: Rp ' . number_format($revenue - $expense, 0, ',', '.'),
            '- Jumlah Transaksi: ' . $txCount,
            '- Rata-rata Order Value: Rp ' . number_format($aov, 0, ',', '.'),
        ];

        return implode("\n", $lines);
    }

    private function topPerformersSection(): string
    {
        $since = Carbon::now()->subDays(30);

        $topProducts = SalesDetail::select('product_id', DB::raw('SUM(quantity) as qty_sold'), DB::raw('SUM(line_total) as revenue'))
            ->whereHas('sale', function ($q) use ($since) {
                $q->where('sale_date', '>=', $since)->where('status', '!=', 'cancelled');
            })
            ->groupBy('product_id')
            ->orderByDesc('qty_sold')
            ->limit(3)
            ->with('product:id,name')
            ->get();

        $topCustomers = Customer::orderByDesc('total_spent')->limit(3)->get(['name', 'total_spent']);

        $lines = ['=== TOP PERFORMERS ==='];

        if ($topProducts->isEmpty()) {
            $lines[] = '- Top Produk (30 hari): belum ada penjualan.';
        } else {
            $lines[] = '- Top Produk Terlaris (30 hari):';
            foreach ($topProducts as $tp) {
                $name = $tp->product?->name ?? '(produk dihapus)';
                $lines[] = '  • ' . $name . ' — ' . (int) $tp->qty_sold . ' pcs, Rp ' . number_format((float) $tp->revenue, 0, ',', '.');
            }
        }

        if ($topCustomers->isEmpty()) {
            $lines[] = '- Top Customer: belum ada data.';
        } else {
            $lines[] = '- Top Customer (total spent):';
            foreach ($topCustomers as $tc) {
                $lines[] = '  • ' . $tc->name . ' — Rp ' . number_format((float) $tc->total_spent, 0, ',', '.');
            }
        }

        return implode("\n", $lines);
    }

    private function trendSection(): string
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayRevenue = (float) Sale::whereDate('sale_date', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $yesterdayRevenue = (float) Sale::whereDate('sale_date', $yesterday)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $deltaPct = $yesterdayRevenue > 0
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100
            : null;

        $weekRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $rev = (float) Sale::whereDate('sale_date', $day)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            $weekRevenue[] = $day->format('D') . ': Rp ' . number_format($rev, 0, ',', '.');
        }

        $lines = ['=== TREND ==='];

        if ($deltaPct === null) {
            $lines[] = '- Perbandingan Hari Ini vs Kemarin: kemarin tidak ada penjualan.';
        } else {
            $arrow = $deltaPct >= 0 ? '↑' : '↓';
            $lines[] = '- Penjualan Hari Ini vs Kemarin: ' . $arrow . ' ' . number_format(abs($deltaPct), 1) . '%';
        }

        $lines[] = '- Pendapatan 7 Hari Terakhir:';
        foreach ($weekRevenue as $row) {
            $lines[] = '  • ' . $row;
        }

        return implode("\n", $lines);
    }
}
