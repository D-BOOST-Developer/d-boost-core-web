<?php

namespace App\Http\Controllers;

use App\Models\DebyAIChatLog;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Services\Deby\DebyAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebyAIController extends Controller
{
    public function __construct(private DebyAIService $deby)
    {
    }

    /**
     * Main AI chat — every message is enriched with the user's business
     * context and recent conversation history before being sent to Groq.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $user = Auth::user();
        $response = $this->deby->chat($user, $request->input('message'));

        $status = $response['success'] ? 200 : 502;
        return response()->json($response, $status);
    }

    public function getChatHistory()
    {
        try {
            $user = Auth::user();

            $chatLogs = DebyAIChatLog::where('user_id', $user->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(fn ($log) => [
                    'id' => $log->id,
                    'role' => $log->role,
                    'message' => $log->message,
                    'metadata' => $log->metadata,
                    'created_at' => $log->created_at->toIso8601String(),
                ]);

            return response()->json([
                'success' => true,
                'history' => $chatLogs,
                'count' => $chatLogs->count(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Get Chat History Error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat chat.',
            ], 500);
        }
    }

    public function deleteChatHistory()
    {
        try {
            $user = Auth::user();
            $deletedCount = DebyAIChatLog::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Riwayat chat berhasil dihapus.',
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Throwable $e) {
            Log::error('Delete Chat History Error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus riwayat chat.',
            ], 500);
        }
    }

    /* ---------------------------------------------------------------------
     | Quick Actions — pure DB queries, NO AI involvement.
     | Each returns the same shape as chat() so the UI can render uniformly.
     | --------------------------------------------------------------------- */

    public function quickRevenue()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $revenue = (float) Sale::whereDate('sale_date', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $count = Sale::whereDate('sale_date', $today)
            ->where('status', '!=', 'cancelled')
            ->count();

        $message = $count > 0
            ? "📊 **Pendapatan Hari Ini**\n\nTotal: **Rp " . number_format($revenue, 0, ',', '.') . "**\nDari {$count} transaksi."
            : "📊 **Pendapatan Hari Ini**\n\nBelum ada penjualan yang tercatat hari ini. Yuk semangat jualan! 💪";

        $this->deby->logQuickAction($user, 'Berapa pendapatan hari ini?', $message, ['type' => 'revenue']);

        return response()->json([
            'success' => true,
            'message' => $message,
            'metadata' => [
                'source' => 'quick_action',
                'type' => 'revenue',
                'action_link' => '/finance/sales',
                'action_label' => 'Lihat Semua Penjualan',
            ],
        ]);
    }

    public function quickExpenses()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $expense = (float) Expense::whereDate('expense_date', $today)->sum('amount');
        $count = Expense::whereDate('expense_date', $today)->count();

        $message = $count > 0
            ? "💸 **Pengeluaran Hari Ini**\n\nTotal: **Rp " . number_format($expense, 0, ',', '.') . "**\nDari {$count} pencatatan."
            : "💸 **Pengeluaran Hari Ini**\n\nBelum ada pengeluaran tercatat hari ini.";

        $this->deby->logQuickAction($user, 'Berapa pengeluaran hari ini?', $message, ['type' => 'expenses']);

        return response()->json([
            'success' => true,
            'message' => $message,
            'metadata' => [
                'source' => 'quick_action',
                'type' => 'expenses',
                'action_link' => '/finance/expenses',
                'action_label' => 'Lihat Semua Pengeluaran',
            ],
        ]);
    }

    public function quickStock()
    {
        $user = Auth::user();

        $totalProducts = Product::count();
        $lowStockCount = Product::lowStock()->count();
        $totalValue = (float) Product::sum(DB::raw('cost * stock_quantity'));
        $outOfStock = Product::where('stock_quantity', 0)->count();

        if ($totalProducts === 0) {
            $message = "📦 **Posisi Stok**\n\nBelum ada produk dalam inventory. Yuk tambah produk pertama Anda!";
        } else {
            $message = "📦 **Posisi Stok**\n\n";
            $message .= "• Total Produk: **{$totalProducts}**\n";
            $message .= "• Nilai Inventory: **Rp " . number_format($totalValue, 0, ',', '.') . "**\n";
            $message .= "• Stok Rendah: **{$lowStockCount}** produk\n";
            $message .= "• Stok Habis: **{$outOfStock}** produk";
        }

        $this->deby->logQuickAction($user, 'Bagaimana posisi stok saat ini?', $message, ['type' => 'stock']);

        return response()->json([
            'success' => true,
            'message' => $message,
            'metadata' => [
                'source' => 'quick_action',
                'type' => 'stock',
                'action_link' => '/inventory/products',
                'action_label' => 'Kelola Produk',
            ],
        ]);
    }

    public function quickSalesToday()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $sales = Sale::whereDate('sale_date', $today)
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('sale_date')
            ->limit(10)
            ->get(['invoice_number', 'total_amount', 'payment_method', 'status']);

        if ($sales->isEmpty()) {
            $message = "🛍️ **Transaksi Hari Ini**\n\nBelum ada transaksi hari ini.";
            $tableData = null;
        } else {
            $message = "🛍️ **Transaksi Hari Ini** ({$sales->count()} transaksi)";
            $tableData = [['Invoice', 'Total', 'Metode', 'Status']];
            foreach ($sales as $s) {
                $tableData[] = [
                    $s->invoice_number,
                    'Rp ' . number_format((float) $s->total_amount, 0, ',', '.'),
                    $s->payment_method ?: '-',
                    $s->status,
                ];
            }
        }

        $this->deby->logQuickAction($user, 'Lihat transaksi penjualan hari ini', $message, ['type' => 'sales_today']);

        return response()->json([
            'success' => true,
            'message' => $message,
            'metadata' => [
                'source' => 'quick_action',
                'type' => 'sales_today',
                'table_data' => $tableData,
                'action_link' => '/finance/sales',
                'action_label' => 'Lihat Semua',
            ],
        ]);
    }

    public function quickProducts()
    {
        $user = Auth::user();

        $products = Product::orderByDesc('stock_quantity')
            ->limit(10)
            ->get(['name', 'stock_quantity', 'price', 'category']);

        if ($products->isEmpty()) {
            $message = "📋 **Daftar Produk**\n\nBelum ada produk. Yuk tambah produk pertama Anda!";
            $tableData = null;
        } else {
            $message = "📋 **Daftar Produk** (top 10 by stok)";
            $tableData = [['Nama', 'Stok', 'Harga', 'Kategori']];
            foreach ($products as $p) {
                $tableData[] = [
                    $p->name,
                    (string) $p->stock_quantity,
                    'Rp ' . number_format((float) $p->price, 0, ',', '.'),
                    $p->category ?: '-',
                ];
            }
        }

        $this->deby->logQuickAction($user, 'Tunjukkan daftar semua produk', $message, ['type' => 'products']);

        return response()->json([
            'success' => true,
            'message' => $message,
            'metadata' => [
                'source' => 'quick_action',
                'type' => 'products',
                'table_data' => $tableData,
                'action_link' => '/inventory/products',
                'action_label' => 'Kelola Produk',
            ],
        ]);
    }

    public function quickLowStock()
    {
        $user = Auth::user();

        $products = Product::lowStock()
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get(['name', 'stock_quantity', 'min_stock_level']);

        if ($products->isEmpty()) {
            $message = "✅ **Status Stok Aman**\n\nSemua produk masih di atas batas minimum. Pertahankan! 🎉";
            $tableData = null;
        } else {
            $message = "⚠️ **Produk Stok Rendah** ({$products->count()} produk perlu restock)";
            $tableData = [['Nama Produk', 'Sisa Stok', 'Min. Stok']];
            foreach ($products as $p) {
                $tableData[] = [
                    $p->name,
                    (string) $p->stock_quantity,
                    (string) $p->min_stock_level,
                ];
            }
        }

        $this->deby->logQuickAction($user, 'Produk mana yang stok rendah?', $message, ['type' => 'low_stock']);

        return response()->json([
            'success' => true,
            'message' => $message,
            'metadata' => [
                'source' => 'quick_action',
                'type' => 'low_stock',
                'table_data' => $tableData,
                'action_link' => '/inventory/products',
                'action_label' => 'Kelola Stok',
            ],
        ]);
    }
}
