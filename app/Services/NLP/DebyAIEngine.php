<?php

namespace App\Services\NLP;

use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DebyAIEngine
{
    private IntentClassifier $intentClassifier;
    private EntityExtractor $entityExtractor;
    private ValidationEngine $validationEngine;
    private KnowledgeBase $knowledgeBase;
    private ConversationContext $context;
    private ContextManager $contextManager;
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->intentClassifier = new IntentClassifier();
        $this->entityExtractor = new EntityExtractor();
        $this->validationEngine = new ValidationEngine();
        $this->knowledgeBase = new KnowledgeBase();
        $this->context = new ConversationContext($user->id);
        $this->contextManager = new ContextManager($user);
    }

    /**
     * Process user input with contextual awareness
     */
    public function process(string $userInput): array
    {
        try {
            $this->context->addToHistory('user', $userInput);

            $intentResult = $this->intentClassifier->classify($userInput);
            $intent = $intentResult['primary_intent'];
            $confidence = $intentResult['confidence'];

            Log::info("DEBY AI Processing", [
                'user_id' => $this->user->id,
                'intent' => $intent,
                'confidence' => $confidence,
                'input' => $userInput,
                'all_scores' => $intentResult['all_scores'] ?? [],
            ]);

            $entities = $this->entityExtractor->extract($userInput);
            $intentMetadata = $this->intentClassifier->getIntentMetadata($intent);

            return $this->routeToHandler($intent, $entities, $intentMetadata);
        } catch (\Exception $e) {
            Log::error("DEBY AI Error", [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'input' => $userInput,
            ]);

            return $this->generateResponse('error', 
                "Maaf, terjadi kesalahan dalam memproses pertanyaan Anda. Silakan coba lagi dengan cara lain. 😔",
                ['type' => 'error', 'error_message' => $e->getMessage()]
            );
        }
    }

    private function routeToHandler(string $intent, array $entities, array $metadata): array
    {
        $category = $metadata['category'] ?? 'unknown';

        try {
            return match ($category) {
                'greeting' => $this->handleGreeting($intent),
                'casual' => $this->handleCasual($intent),
                'inventory' => $this->handleInventory($intent, $entities),
                'finance' => $this->handleFinance($intent, $entities),
                'analytics' => $this->handleAnalytics($intent, $entities),
                'crm' => $this->handleCRM($intent, $entities),
                'supplier' => $this->handleSupplier($intent, $entities),
                'hr' => $this->handleHR($intent, $entities),
                'marketing' => $this->handleMarketing($intent, $entities),
                'reports' => $this->handleReports($intent, $entities),
                default => $this->handleUnknown($intent),
            };
        } catch (\Exception $e) {
            Log::error("DEBY AI Handler Error", [
                'intent' => $intent,
                'category' => $category,
                'error' => $e->getMessage(),
            ]);
            
            return $this->handleUnknown($intent);
        }
    }

    // ===== GREETING HANDLERS =====

    private function handleGreeting(string $intent): array
    {
        $responses = match ($intent) {
            'greeting.hello' => [
                "Halo! 👋 Selamat datang di DEBY AI. Saya siap membantu Anda mengelola bisnis dengan lebih efisien.",
                "Hai! 😊 Ada yang bisa saya bantu hari ini? Tanyakan tentang stok, penjualan, pelanggan, atau apapun yang Anda butuhkan.",
                "Selamat datang! 🎉 Saya adalah asisten AI Anda. Apa yang ingin Anda ketahui tentang bisnis Anda?",
            ],
            'greeting.introduction' => [
                "Nama saya DEBY AI 🤖, saya adalah asisten cerdas untuk Smart Data Analysis Dashboard UMKM Anda. Saya bisa membantu Anda dengan:\n• 📦 Manajemen Inventori\n• 💰 Analisis Keuangan\n• 📊 Laporan Penjualan\n• 👥 Manajemen Pelanggan\n• 📈 Analitik Bisnis\n\nApa yang ingin Anda tanyakan?",
            ],
            'greeting.affirmation' => [
                "Sama-sama! 😊 Senang bisa membantu Anda.",
                "Terima kasih! Saya siap membantu kapan saja. 💪",
                "Mantap! Jika ada yang lain, jangan ragu untuk bertanya. 👍",
            ],
            'greeting.help_request' => [
                "Saya bisa membantu Anda dengan:\n\n📦 **INVENTORY**: Cek stok, tambah produk, update stok\n💰 **FINANCE**: Catat pengeluaran, lihat pendapatan, hitung laba rugi\n📊 **ANALYTICS**: Lihat penjualan hari ini, produk terlaris, tren minggu\n👥 **CRM**: Cari pelanggan, kirim broadcast, buat follow-up\n🏭 **SUPPLIER**: Daftar supplier, buat PO\n👨‍💼 **HR**: Daftar karyawan, absensi\n📈 **MARKETING**: Kampanye, performa\n\nTanyakan saja apa yang Anda butuhkan! 😊",
            ],
            default => "Ada yang bisa saya bantu? 😊",
        };

        $message = is_array($responses) ? $responses[array_rand($responses)] : $responses;
        $this->context->addToHistory('assistant', $message);

        return $this->generateResponse('greeting', $message, ['type' => $intent]);
    }

    // ===== CASUAL HANDLERS =====

    private function handleCasual(string $intent): array
    {
        $responses = match ($intent) {
            'casual.complaint' => [
                "Maaf mendengar ada kendala. 😟 Bisa jelaskan lebih detail masalahnya? Saya siap membantu mencari solusinya.",
                "Ada yang tidak berfungsi? Tolong ceritakan lebih detail agar saya bisa membantu mengatasi masalahnya.",
            ],
            'casual.sharing' => [
                "Saya mendengarkan! 👂 Silakan ceritakan, mungkin saya bisa memberikan insight atau saran yang berguna untuk bisnis Anda.",
                "Oke, saya siap mendengarkan. Ceritakan saja apa yang ingin Anda bagikan. 😊",
            ],
            default => "Saya mengerti. Ada yang bisa saya bantu? 😊",
        };

        $message = is_array($responses) ? $responses[array_rand($responses)] : $responses;
        $this->context->addToHistory('assistant', $message);

        return $this->generateResponse('casual', $message, ['type' => $intent]);
    }

    // ===== INVENTORY HANDLERS =====

    private function handleInventory(string $intent, array $entities): array
    {
        return match ($intent) {
            'inventory.check_stock' => $this->handleCheckStock($entities),
            'inventory.update_stock' => $this->handleUpdateStock($entities),
            'inventory.add_product' => $this->handleAddProduct($entities),
            'inventory.low_stock_alert' => $this->handleLowStockAlert($entities),
            'inventory.product_list' => $this->handleProductList($entities),
            'inventory.product_search' => $this->handleProductSearch($entities),
            default => $this->handleUnknown($intent),
        };
    }

    private function handleCheckStock(array $entities): array
    {
        $productName = $entities['product_name'] ?? null;

        if ($productName) {
            // Search in database from products table
            $product = Product::where('name', 'like', "%{$productName}%")->first();
            
            if ($product) {
                $stock = $product->stock_quantity ?? 0;
                $status = $stock > 30 ? '✅ Stok tersedia dengan baik' : 
                         ($stock > 10 ? '⚠️ Stok terbatas' : '🔴 Stok rendah');
                
                $message = "📦 **Stok {$product->name}**\n\n• Jumlah: {$stock} unit\n• Harga: Rp " . number_format($product->price, 0) . "\n• Status: {$status}\n\nApakah Anda ingin melakukan pemesanan atau update stok?";
                return $this->generateResponse('data', $message, ['type' => 'check_stock', 'product_id' => $product->id]);
            }
        }

        // Show all products if no specific product
        return $this->handleStockOnly($entities);
    }

    private function handleStockOnly(array $entities): array
    {
        $productsData = $this->contextManager->getAllProducts();
        
        if (empty($productsData)) {
            $message = "Belum ada produk di sistem. 😊";
            return $this->generateResponse('info', $message, ['type' => 'stock_list_empty']);
        }

        $message = "📦 **Posisi Stok Saat Ini** (" . count($productsData) . " produk)\n\n";
        
        $tableData = [['No', 'Produk', 'Stok']];
        $no = 1;
        foreach ($productsData as $product) {
            $tableData[] = [
                $no++,
                $product['name'],
                $product['stock'] . ' unit',
            ];
        }

        $message .= "💡 **Tip**: Gunakan 'Alert' untuk melihat produk dengan stok rendah.";

        return $this->generateResponse('data', $message, [
            'type' => 'stock_only',
            'table_data' => $tableData,
            'action_link' => '/inventory/products',
            'action_label' => 'Lihat Semua Produk',
        ]);
    }

    private function handleProductList(array $entities): array
    {
        $productsData = $this->contextManager->getAllProducts();
        
        if (empty($productsData)) {
            $message = "Belum ada produk di sistem. Ingin menambah produk baru? 📝\n\nGunakan perintah: 'Tambah produk [nama] harga [harga] stok [jumlah]'";
            return $this->generateResponse('info', $message, ['type' => 'product_list_empty']);
        }

        $stockSummary = $this->contextManager->getStockSummary();
        $insight = $this->generateStockInsight($stockSummary);
        
        $message = "� **Daftar Produk Lengkap** (" . count($productsData) . " produk)\n\n";
        $message .= "📊 **Ringkasan Stok**:\n";
        $message .= "• Stok Baik: " . $stockSummary['good'] . " produk\n";
        $message .= "• Stok Terbatas: " . $stockSummary['limited'] . " produk\n";
        $message .= "• Stok Rendah: " . $stockSummary['low'] . " produk\n\n";
        
        $tableData = [['No', 'Produk', 'Kategori', 'Stok', 'Harga', 'Status']];
        $no = 1;
        foreach ($productsData as $product) {
            $statusIcon = $product['status'] === 'Baik' ? '✅' : 
                         ($product['status'] === 'Terbatas' ? '⚠️' : '🔴');
            
            $tableData[] = [
                $no++,
                $product['name'],
                $product['category'] ?? '-',
                $product['stock'] . ' unit',
                $product['formatted_price'],
                $statusIcon . ' ' . $product['status'],
            ];
        }

        $message .= "💡 **Insight**: {$insight}";

        return $this->generateResponse('data', $message, [
            'type' => 'product_list',
            'table_data' => $tableData,
            'action_link' => '/inventory/products',
            'action_label' => 'Lihat Semua Produk',
        ]);
    }

    private function handleProductSearch(array $entities): array
    {
        $productName = $entities['product_name'] ?? null;

        if (!$productName) {
            $message = "Produk apa yang ingin Anda cari? 🔍";
            return $this->generateResponse('question', $message, ['type' => 'product_search']);
        }

        $products = Product::where('name', 'like', "%{$productName}%")->limit(5)->get();

        if ($products->isEmpty()) {
            $message = "Produk '{$productName}' tidak ditemukan. 😞 Ingin menambah produk baru atau cari produk lain?";
            return $this->generateResponse('info', $message, ['type' => 'product_not_found']);
        }

        $list = "🔍 **Hasil Pencarian untuk '{$productName}'**\n\n";
        foreach ($products as $product) {
            $list .= "• **{$product->name}** - Rp " . number_format($product->price, 0) . " (Stok: {$product->stock})\n";
        }

        $message = $list . "\nPilih salah satu untuk detail lebih lanjut.";
        return $this->generateResponse('data', $message, ['type' => 'product_search']);
    }

    private function handleUpdateStock(array $entities): array
    {
        $productName = $entities['product_name'] ?? null;
        $quantity = $entities['quantity'] ?? 0;

        if (!$productName || $quantity <= 0) {
            $message = "Untuk update stok, saya butuh:\n• Nama produk\n• Jumlah stok\n\nContoh: 'Tambah stok Nasi Uduk 20 unit'";
            return $this->generateResponse('question', $message, ['type' => 'update_stock_incomplete']);
        }

        $message = "✅ Stok {$productName} akan diperbarui +{$quantity} unit.\n\nKonfirmasi perubahan ini? (Ketik 'ya' atau 'tidak')";
        return $this->generateResponse('confirmation', $message, ['type' => 'update_stock']);
    }

    private function handleAddProduct(array $entities): array
    {
        $productName = $entities['product_name'] ?? null;
        $price = $entities['price'] ?? 0;
        $quantity = $entities['quantity'] ?? 0;

        if (!$productName) {
            $message = "Untuk menambah produk, saya butuh informasi:\n• Nama produk\n• Harga\n• Stok awal\n\nContoh: 'Tambah produk Nasi Uduk harga 15000 stok 50'";
            return $this->generateResponse('question', $message, ['type' => 'add_product_incomplete']);
        }

        $message = "📝 **Produk Baru**\n• Nama: {$productName}\n• Harga: Rp " . number_format($price, 0) . "\n• Stok Awal: {$quantity} unit\n\nTambahkan produk ini? (Ketik 'ya' atau 'tidak')";
        return $this->generateResponse('confirmation', $message, ['type' => 'add_product']);
    }

    private function handleLowStockAlert(array $entities): array
    {
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)->get();
        $stockSummary = $this->contextManager->getStockSummary();

        if ($lowStockProducts->isEmpty()) {
            $insight = $this->generateStockInsight($stockSummary);
            $message = "✅ Semua produk memiliki stok yang cukup. Tidak ada alert stok rendah atau terbatas saat ini.\n\n💡 {$insight}";
            return $this->generateResponse('info', $message, ['type' => 'low_stock_alert_none']);
        }

        $message = "⚠️ **Alert Stok Produk** (" . count($lowStockProducts) . " produk)\n\n";
        $message .= "🔴 **Status Produk yang Tidak Baik**:\n";
        $message .= "• Stok Rendah: Produk dengan stok ≤ 10 unit\n";
        $message .= "• Stok Terbatas: Produk dengan stok 11-30 unit\n\n";
        
        $tableData = [['No', 'Produk', 'Stok Saat Ini', 'Min Stok', 'Kategori', 'Status']];
        $no = 1;
        
        foreach ($lowStockProducts as $product) {
            if ($product->stock_quantity <= 0) {
                $status = '🔴 Habis';
            } elseif ($product->stock_quantity <= 10) {
                $status = '🔴 Rendah';
            } else {
                $status = '⚠️ Terbatas';
            }
            
            $tableData[] = [
                $no++,
                $product->name,
                $product->stock_quantity . ' unit',
                $product->min_stock_level . ' unit',
                $product->category ?? '-',
                $status
            ];
        }

        $insight = $this->generateStockInsight($stockSummary);
        $message .= "💡 **Insight**: {$insight}\n\n⚡ **Rekomendasi**: Segera lakukan restock untuk produk-produk ini untuk menghindari kehilangan penjualan.";
        
        return $this->generateResponse('alert', $message, [
            'type' => 'low_stock_alert',
            'table_data' => $tableData,
            'action_link' => '/inventory/products',
            'action_label' => 'Lihat Semua Produk',
        ]);
    }

    // ===== FINANCE HANDLERS =====

    private function handleFinance(string $intent, array $entities): array
    {
        return match ($intent) {
            'finance.log_expense' => $this->handleLogExpense($entities),
            'finance.check_revenue' => $this->handleCheckRevenue($entities),
            'finance.calculate_profit_loss' => $this->handleProfitLoss($entities),
            'finance.expense_list' => $this->handleExpenseList($entities),
            'finance.sales_list' => $this->handleSalesList($entities),
            default => $this->handleUnknown($intent),
        };
    }

    private function handleCheckRevenue(array $entities): array
    {
        try {
            $revenueData = $this->contextManager->getTodayRevenue();
            
            // Check if no sales today
            if ($revenueData['total'] == 0) {
                $message = "💵 **Pendapatan Hari Ini** (" . date('d/m/Y') . ")\n\n";
                $message .= "Belum ada penjualan hari ini. 😊\n\n";
                $message .= "💡 Mulai tingkatkan penjualan Anda dengan strategi marketing yang lebih baik!";
                
                return $this->generateResponse('info', $message, [
                    'type' => 'revenue_check_empty',
                    'table_data' => [
                        ['Metrik', 'Nilai'],
                        ['Total Pendapatan', 'Rp 0'],
                        ['Jumlah Transaksi', '0'],
                        ['Rata-rata Transaksi', 'Rp 0'],
                    ],
                ]);
            }
            
            $yesterdayRevenue = Sale::whereDate('sale_date', date('Y-m-d', strtotime('-1 day')))->sum('total_amount');
            $change = $yesterdayRevenue > 0 ? (($revenueData['total'] - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 0;
            $trend = $change >= 0 ? "↑ Meningkat" : "↓ Menurun";
            $trendEmoji = $change >= 0 ? "📈" : "📉";
            $insight = $this->generateRevenueInsight($revenueData, $change);
            
            $message = "💵 **Total Pendapatan Hari Ini** (" . date('d/m/Y') . ")\n\n";
            $message .= "💰 **Total Keseluruhan: " . $revenueData['formatted_total'] . "**\n";
            $message .= "📊 Jumlah Transaksi: " . $revenueData['transactions'] . " transaksi\n";
            $message .= "📈 Rata-rata: " . $revenueData['formatted_average'] . " per transaksi\n";
            $message .= "{$trendEmoji} Trend vs Kemarin: {$trend} " . number_format(abs($change), 1) . "%\n\n";
            $message .= "💡 **Insight**: {$insight}";
            
            // Get transactions data to show in table
            $transactionsData = $this->contextManager->getTodayTransactions();
            $tableData = [['Waktu', 'Pelanggan', 'Jumlah']];
            
            // Add transactions if available
            if (!empty($transactionsData['data'])) {
                foreach ($transactionsData['data'] as $transaction) {
                    $tableData[] = [
                        $transaction['time'],
                        $transaction['customer'],
                        $transaction['formatted_amount'],
                    ];
                }
                $tableData[] = ['', 'TOTAL', $revenueData['formatted_total']];
            }
            
            return $this->generateResponse('data', $message, [
                'type' => 'revenue_check',
                'table_data' => $tableData,
                'action_link' => '/finance/sales',
                'action_label' => 'Lihat Semua Penjualan',
            ]);
        } catch (\Exception $e) {
            Log::error('handleCheckRevenue error', ['error' => $e->getMessage()]);
            return $this->generateResponse('error', 'Gagal mengambil data pendapatan. Silakan coba lagi.', ['type' => 'error']);
        }
    }

    private function handleProfitLoss(array $entities): array
    {
        try {
            $profitData = $this->contextManager->getTodayProfit();
            $insight = $this->generateProfitInsight($profitData);
            
            $message = "📊 **Laporan Laba Rugi Hari Ini** (" . date('d/m/Y') . ")\n\n";
            $message .= "• Pendapatan: " . $this->contextManager->getTodayRevenue()['formatted_total'] . "\n";
            $message .= "• Pengeluaran: " . $this->contextManager->getTodayExpenses()['formatted_total'] . "\n";
            $message .= "• Laba Bersih: " . $profitData['formatted_total'] . "\n";
            $message .= "• Margin Keuntungan: " . $profitData['formatted_margin'] . "\n\n";
            $message .= "💡 **Insight**: {$insight}";
            
            return $this->generateResponse('data', $message, [
                'type' => 'profit_loss',
                'table_data' => [
                    ['Metrik', 'Nilai'],
                    ['Pendapatan', $this->contextManager->getTodayRevenue()['formatted_total']],
                    ['Pengeluaran', $this->contextManager->getTodayExpenses()['formatted_total']],
                    ['Laba Bersih', $profitData['formatted_total']],
                    ['Margin Keuntungan', $profitData['formatted_margin']],
                    ['Status', $profitData['status']],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('handleProfitLoss error', ['error' => $e->getMessage()]);
            return $this->generateResponse('error', 'Gagal menghitung laba rugi. Silakan coba lagi.', ['type' => 'error']);
        }
    }

    private function handleLogExpense(array $entities): array
    {
        $amount = $entities['price'] ?? 0;
        $category = $entities['expense_category'] ?? 'umum';

        if ($amount <= 0) {
            $message = "Berapa jumlah pengeluaran yang ingin dicatat? (Contoh: 'Catat pengeluaran 50000')";
            return $this->generateResponse('question', $message, ['type' => 'log_expense_incomplete']);
        }

        $message = "💰 **Pengeluaran Baru**\n• Jumlah: Rp " . number_format($amount, 0) . "\n• Kategori: {$category}\n• Tanggal: " . date('d/m/Y') . "\n\nCatat pengeluaran ini? (Ketik 'ya' atau 'tidak')";
        return $this->generateResponse('confirmation', $message, ['type' => 'log_expense']);
    }

    private function handleExpenseList(array $entities): array
    {
        try {
            $expensesData = $this->contextManager->getAllExpensesToday();

            if (empty($expensesData)) {
                $insight = $this->generateExpenseInsight([], 0);
                $message = "✨ Belum ada pengeluaran hari ini. Pertahankan efisiensi biaya Anda! 😊\n\n💡 **Insight**: {$insight}";
                return $this->generateResponse('info', $message, ['type' => 'expense_list_empty']);
            }

            $total = 0;
            foreach ($expensesData as $expense) {
                $total += $expense['amount'];
            }

            $revenue = $this->contextManager->getTodayRevenue()['total'];
            $insight = $this->generateExpenseInsight($expensesData, $total, $revenue);
            $message = "💸 **Total Pengeluaran Hari Ini** (" . date('d/m/Y') . ")\n\n";
            $message .= "💰 **Total Keseluruhan: Rp " . number_format($total, 0) . "**\n";
            $message .= "📊 Jumlah Item: " . count($expensesData) . " pengeluaran\n\n";
            $message .= "💡 **Insight**: {$insight}";
            
            $tableData = [['Waktu', 'Kategori', 'Deskripsi', 'Jumlah']];
            foreach ($expensesData as $expense) {
                $tableData[] = [
                    $expense['time'],
                    ucfirst($expense['category']),
                    $expense['description'] ?? '-',
                    $expense['formatted_amount'],
                ];
            }
            $tableData[] = ['', '', 'TOTAL', 'Rp ' . number_format($total, 0)];

            return $this->generateResponse('data', $message, [
                'type' => 'expense_list',
                'table_data' => $tableData,
                'action_link' => '/finance/expenses',
                'action_label' => 'Lihat Semua Pengeluaran',
            ]);
        } catch (\Exception $e) {
            Log::error('handleExpenseList error', ['error' => $e->getMessage()]);
            return $this->generateResponse('error', 'Gagal mengambil data pengeluaran. Silakan coba lagi.', ['type' => 'error']);
        }
    }

    private function handleSalesList(array $entities): array
    {
        try {
            $transactionsData = $this->contextManager->getTodayTransactions();

            if (empty($transactionsData['data'])) {
                $message = "Belum ada penjualan hari ini. 😊\n\n💡 Mulai tingkatkan penjualan Anda dengan strategi marketing yang lebih baik!";
                return $this->generateResponse('info', $message, ['type' => 'sales_list_empty']);
            }

            $total = 0;
            foreach ($transactionsData['data'] as $transaction) {
                $total += $transaction['amount'];
            }
            
            $avgTransaction = count($transactionsData['data']) > 0 ? $total / count($transactionsData['data']) : 0;
            
            $message = "🛍️ **Transaksi Penjualan Hari Ini** (" . count($transactionsData['data']) . " transaksi)\n\n";
            $message .= "• Total Penjualan: Rp " . number_format($total, 0) . "\n";
            $message .= "• Rata-rata Transaksi: Rp " . number_format($avgTransaction, 0) . "\n";
            $message .= "• Jumlah Pelanggan: " . count($transactionsData['data']) . "\n\n";
            $message .= "💡 **Insight**: Anda telah melayani " . count($transactionsData['data']) . " transaksi hari ini. Pertahankan momentum ini! 🚀";
            
            $tableData = [['Waktu', 'Pelanggan', 'Jumlah']];
            foreach ($transactionsData['data'] as $transaction) {
                $tableData[] = [
                    $transaction['time'],
                    $transaction['customer'],
                    $transaction['formatted_amount'],
                ];
            }
            $tableData[] = ['', 'TOTAL', 'Rp ' . number_format($total, 0)];

            return $this->generateResponse('data', $message, [
                'type' => 'sales_list',
                'table_data' => $tableData,
                'action_link' => '/finance/sales',
                'action_label' => 'Lihat Semua Penjualan',
            ]);
        } catch (\Exception $e) {
            Log::error('handleSalesList error', ['error' => $e->getMessage()]);
            return $this->generateResponse('error', 'Gagal mengambil data transaksi. Silakan coba lagi.', ['type' => 'error']);
        }
    }

    // ===== ANALYTICS HANDLERS =====

    private function handleAnalytics(string $intent, array $entities): array
    {
        return match ($intent) {
            'analytics.daily_sales' => $this->handleDailySales($entities),
            'analytics.top_products' => $this->handleTopProducts($entities),
            'analytics.weekly_trend' => $this->handleWeeklyTrend($entities),
            'analytics.monthly_comparison' => $this->handleMonthlyComparison($entities),
            'analytics.performance' => $this->handlePerformance($entities),
            default => $this->handleUnknown($intent),
        };
    }

    private function handleDailySales(array $entities): array
    {
        $today = date('Y-m-d');
        $sales = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $transactions = Sale::whereDate('sale_date', $today)->count();
        $avgSale = $transactions > 0 ? $sales / $transactions : 0;

        $topProduct = Sale::whereDate('sale_date', $today)
            ->selectRaw('product_name, COUNT(*) as count')
            ->groupBy('product_name')
            ->orderByRaw('COUNT(*) DESC')
            ->first();

        $topProductName = $topProduct ? $topProduct->product_name . " ({$topProduct->count} penjualan)" : "Belum ada";
        $status = $sales > 0 ? "Hari yang baik! 😊" : "Belum ada penjualan hari ini.";
        
        $message = "📈 **Penjualan Hari Ini** (" . date('d/m/Y') . ")\n\n";
        $message .= "• Total: Rp " . number_format($sales, 0) . "\n";
        $message .= "• Transaksi: {$transactions}\n";
        $message .= "• Rata-rata: Rp " . number_format($avgSale, 0) . "\n\n";
        $message .= "🏆 Produk Terlaris: {$topProductName}\n\n";
        $message .= "💡 **Status**: {$status}";
        
        $tableData = [['Metrik', 'Nilai'],
            ['Total Penjualan', 'Rp ' . number_format($sales, 0)],
            ['Jumlah Transaksi', $transactions],
            ['Rata-rata Transaksi', 'Rp ' . number_format($avgSale, 0)],
            ['Produk Terlaris', $topProductName],
        ];
        
        return $this->generateResponse('data', $message, [
            'type' => 'daily_sales',
            'table_data' => $tableData,
        ]);
    }

    private function handleTopProducts(array $entities): array
    {
        $topProducts = Sale::whereDate('sale_date', date('Y-m-d'))
            ->selectRaw('product_name, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('product_name')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->get();

        if ($topProducts->isEmpty()) {
            $message = "Belum ada data penjualan. 😊\n\n💡 Mulai catat penjualan Anda untuk melihat produk terlaris!";
            return $this->generateResponse('info', $message, ['type' => 'top_products_empty']);
        }

        $list = "🏆 **Top Produk Terlaris**\n\n";
        $no = 1;
        $totalRevenue = 0;
        $totalUnits = 0;
        
        foreach ($topProducts as $product) {
            $list .= "{$no}️⃣ {$product->product_name}\n   • Penjualan: {$product->count} unit\n   • Revenue: Rp " . number_format($product->revenue, 0) . "\n\n";
            $totalRevenue += $product->revenue;
            $totalUnits += $product->count;
            $no++;
        }

        $message = $list . "💡 **Insight**: Produk-produk ini adalah favorit pelanggan Anda! Total revenue dari top 5 produk: Rp " . number_format($totalRevenue, 0) . " 🎯";
        
        $tableData = [['Ranking', 'Produk', 'Penjualan', 'Revenue']];
        $no = 1;
        foreach ($topProducts as $product) {
            $tableData[] = [
                $no,
                $product->product_name,
                $product->count . ' unit',
                'Rp ' . number_format($product->revenue, 0),
            ];
            $no++;
        }
        
        return $this->generateResponse('data', $message, [
            'type' => 'top_products',
            'table_data' => $tableData,
        ]);
    }

    private function handleWeeklyTrend(array $entities): array
    {
        // Get sales data for current week (last 7 days)
        $weekData = [];
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $totalThisWeek = 0;
        $maxDay = '';
        $maxAmount = 0;
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dailySales = Sale::whereDate('sale_date', $date)->sum('total_amount');
            $dayName = $days[(7 - $i - 1) % 7];
            $weekData[] = [
                'day' => $dayName,
                'amount' => $dailySales,
                'formatted' => 'Rp ' . number_format($dailySales, 0)
            ];
            $totalThisWeek += $dailySales;
            
            if ($dailySales > $maxAmount) {
                $maxAmount = $dailySales;
                $maxDay = $dayName;
            }
        }
        
        // Calculate last week total for comparison
        $lastWeekStart = date('Y-m-d', strtotime('-13 days'));
        $lastWeekEnd = date('Y-m-d', strtotime('-7 days'));
        $totalLastWeek = Sale::whereBetween('sale_date', [$lastWeekStart, $lastWeekEnd])->sum('total_amount');
        $weekChange = $totalLastWeek > 0 ? (($totalThisWeek - $totalLastWeek) / $totalLastWeek) * 100 : 0;
        $trend = $weekChange >= 0 ? "↑ +" . number_format(abs($weekChange), 1) . "%" : "↓ " . number_format(abs($weekChange), 1) . "%";
        
        $message = "📊 **Tren Penjualan Minggu Ini**\n\n";
        foreach ($weekData as $data) {
            $indicator = $data['day'] === $maxDay ? "⬆️ (Tertinggi)" : ($data['amount'] > 0 ? "↑" : "");
            $message .= "{$data['day']}: {$data['formatted']} {$indicator}\n";
        }
        $message .= "\n📈 Trend: {$trend} dibanding minggu lalu\n";
        $message .= "💡 Insight: " . ($maxDay ? "Penjualan tertinggi di hari {$maxDay}" : "Belum ada data penjualan minggu ini");
        
        return $this->generateResponse('data', $message, ['type' => 'weekly_trend']);
    }

    private function handleMonthlyComparison(array $entities): array
    {
        // This month data
        $thisMonthStart = date('Y-m-01');
        $thisMonthEnd = date('Y-m-t');
        $thisMonthSales = Sale::whereBetween('sale_date', [$thisMonthStart, $thisMonthEnd])->sum('total_amount');
        $thisMonthTransactions = Sale::whereBetween('sale_date', [$thisMonthStart, $thisMonthEnd])->count();
        $thisMonthCustomers = Customer::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->count();
        $thisMonthAvg = $thisMonthTransactions > 0 ? $thisMonthSales / $thisMonthTransactions : 0;
        
        // Last month data
        $lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
        $lastMonthEnd = date('Y-m-t', strtotime('last day of last month'));
        $lastMonthSales = Sale::whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])->sum('total_amount');
        $lastMonthTransactions = Sale::whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])->count();
        $lastMonthCustomers = Customer::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $lastMonthAvg = $lastMonthTransactions > 0 ? $lastMonthSales / $lastMonthTransactions : 0;
        
        // Calculate changes
        $salesChange = $lastMonthSales > 0 ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100 : 0;
        $customersChange = $lastMonthCustomers > 0 ? (($thisMonthCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100 : 0;
        $avgChange = $lastMonthAvg > 0 ? (($thisMonthAvg - $lastMonthAvg) / $lastMonthAvg) * 100 : 0;
        
        $salesTrend = $salesChange >= 0 ? "+" . number_format($salesChange, 1) : number_format($salesChange, 1);
        $customersTrend = $customersChange >= 0 ? "+" . number_format($customersChange, 1) : number_format($customersChange, 1);
        $avgTrend = $avgChange >= 0 ? "+" . number_format($avgChange, 1) : number_format($avgChange, 1);
        
        $message = "📊 **Perbandingan Bulan Ini vs Bulan Lalu**\n\n";
        $message .= "💰 Penjualan: {$salesTrend}% (Rp " . number_format($lastMonthSales / 1000000, 1) . "M → Rp " . number_format($thisMonthSales / 1000000, 1) . "M)\n";
        $message .= "👥 Pelanggan Baru: {$customersTrend}% ({$lastMonthCustomers} → {$thisMonthCustomers} orang)\n";
        $message .= "💵 Rata-rata Transaksi: {$avgTrend}% (Rp " . number_format($lastMonthAvg / 1000, 0) . "K → Rp " . number_format($thisMonthAvg / 1000, 0) . "K)\n";
        $message .= "📊 Jumlah Transaksi: ({$lastMonthTransactions} → {$thisMonthTransactions})\n\n";
        
        $conclusion = $salesChange >= 0 ? "✅ Kesimpulan: Bisnis Anda berkembang dengan baik!" : "⚠️ Penjualan menurun. Tingkatkan strategi marketing!";
        $message .= $conclusion . "\n\n🎯 Rekomendasi: " . ($salesChange >= 0 ? "Tingkatkan stok produk terlaris" : "Evaluasi strategi penjualan dan promosi");
        
        return $this->generateResponse('data', $message, ['type' => 'monthly_comparison']);
    }

    private function handlePerformance(array $entities): array
    {
        $todaySales = Sale::whereDate('sale_date', date('Y-m-d'))->sum('total_amount');
        $yesterdaySales = Sale::whereDate('sale_date', date('Y-m-d', strtotime('-1 day')))->sum('total_amount');
        $change = $yesterdaySales > 0 ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100 : 0;

        $message = "📊 **Performa Bisnis Anda**\n\n• Penjualan Hari Ini: Rp " . number_format($todaySales, 0) . "\n• Penjualan Kemarin: Rp " . number_format($yesterdaySales, 0) . "\n• Perubahan: " . ($change >= 0 ? "↑" : "↓") . " " . number_format(abs($change), 1) . "%\n\n" . ($change >= 0 ? "✅ Performa meningkat!" : "⚠️ Performa menurun, tingkatkan strategi penjualan.");
        return $this->generateResponse('data', $message, ['type' => 'performance']);
    }

    // ===== CRM HANDLERS =====

    private function handleCRM(string $intent, array $entities): array
    {
        return $this->generateResponse('info', "Maaf, fitur manajemen pelanggan (CRM) saat ini telah dinonaktifkan dari sistem. 😊", ['type' => 'crm_disabled']);
    }

    private function handleSearchCustomer(array $entities): array
    {
        $customerName = $entities['customer_name'] ?? null;

        if (!$customerName) {
            $message = "Nama pelanggan siapa yang ingin Anda cari? 🔍";
            return $this->generateResponse('question', $message, ['type' => 'search_customer']);
        }

        $customer = Customer::where('name', 'like', "%{$customerName}%")->first();

        if (!$customer) {
            $message = "Pelanggan '{$customerName}' tidak ditemukan. 😞 Ingin menambah pelanggan baru?";
            return $this->generateResponse('info', $message, ['type' => 'customer_not_found']);
        }

        $totalPurchase = Sale::where('customer_id', $customer->id)->sum('total_amount');
        $transactions = Sale::where('customer_id', $customer->id)->count();
        $avgPurchase = $transactions > 0 ? $totalPurchase / $transactions : 0;

        $message = "👤 **Data Pelanggan: {$customer->name}**\n\n• Total Pembelian: Rp " . number_format($totalPurchase, 0) . "\n• Jumlah Transaksi: {$transactions}\n• Rata-rata: Rp " . number_format($avgPurchase, 0) . "\n• Status: " . ($transactions > 10 ? "Pelanggan Setia ⭐" : "Pelanggan Baru 🆕") . "\n\nApakah Anda ingin melihat detail lebih lanjut atau menambah follow-up?";
        return $this->generateResponse('data', $message, ['type' => 'customer_search']);
    }

    private function handleCustomerList(array $entities): array
    {
        $customersData = $this->contextManager->getAllCustomers();

        if (empty($customersData)) {
            $message = "Belum ada pelanggan di sistem. 😊\n\n💡 Mulai tambahkan pelanggan Anda untuk memulai perjalanan bisnis yang lebih baik!";
            return $this->generateResponse('info', $message, ['type' => 'customer_list_empty']);
        }

        $totalCustomers = count($customersData);
        $totalPurchase = 0;
        $activeCustomers = 0;
        foreach ($customersData as $customer) {
            $totalPurchase += $customer['total_purchase'] ?? 0;
            if ($customer['status'] === 'Aktif') {
                $activeCustomers++;
            }
        }
        
        $avgPurchase = $totalCustomers > 0 ? $totalPurchase / $totalCustomers : 0;
        
        $message = "👥 **Total Pelanggan Saat Ini** (" . $totalCustomers . " pelanggan)\n\n";
        $message .= "📊 **Statistik Pelanggan**:\n";
        $message .= "• Total Pelanggan: {$totalCustomers} orang\n";
        $message .= "• Pelanggan Aktif: {$activeCustomers} orang\n";
        $message .= "• Total Pembelian: Rp " . number_format($totalPurchase, 0) . "\n";
        $message .= "• Rata-rata Pembelian per Pelanggan: Rp " . number_format($avgPurchase, 0) . "\n\n";
        $message .= "💡 **Insight**: Anda memiliki " . $totalCustomers . " pelanggan. Fokus pada " . $activeCustomers . " pelanggan aktif untuk meningkatkan penjualan!";
        
        $tableData = [['No', 'Nama', 'Email', 'Telepon', 'Kota', 'Transaksi', 'Total Pembelian', 'Pembelian Terakhir', 'Status']];
        $no = 1;
        foreach ($customersData as $customer) {
            $tableData[] = [
                $no++,
                $customer['name'],
                $customer['email'] ?? '-',
                $customer['phone'] ?? '-',
                $customer['city'] ?? '-',
                $customer['transactions'],
                $customer['formatted_purchase'],
                $customer['last_purchase_date'],
                $customer['status'],
            ];
        }

        return $this->generateResponse('data', $message, [
            'type' => 'customer_list',
            'table_data' => $tableData,
            'action_link' => '/customers',
            'action_label' => 'Lihat Semua Pelanggan',
        ]);
    }

    private function handleSendBroadcast(array $entities): array
    {
        $totalCustomers = Customer::count();
        $message = "📢 **Broadcast Pesan**\n\nPenerima: Semua Pelanggan ({$totalCustomers} orang)\n\nKonfirmasi pengiriman broadcast ini? (Ketik 'ya' atau 'tidak')";
        return $this->generateResponse('confirmation', $message, ['type' => 'send_broadcast']);
    }

    private function handleAddFollowup(array $entities): array
    {
        $customerName = $entities['customer_name'] ?? null;

        if (!$customerName) {
            $message = "Follow-up untuk pelanggan siapa? 📅";
            return $this->generateResponse('question', $message, ['type' => 'add_followup']);
        }

        $message = "📅 **Follow-up Baru**\n• Pelanggan: {$customerName}\n• Tanggal: " . date('d/m/Y') . "\n• Waktu: 10:00 AM\n\nBuat follow-up ini? (Ketik 'ya' atau 'tidak')";
        return $this->generateResponse('confirmation', $message, ['type' => 'add_followup']);
    }

    // ===== SUPPLIER HANDLERS =====

    private function handleSupplier(string $intent, array $entities): array
    {
        return match ($intent) {
            'supplier.list_suppliers' => $this->handleListSuppliers($entities),
            'supplier.create_purchase_order' => $this->handleCreatePurchaseOrder($entities),
            default => $this->handleUnknown($intent),
        };
    }

    private function handleListSuppliers(array $entities): array
    {
        // Get suppliers from database (assuming Supplier model exists)
        try {
            $suppliers = \App\Models\Supplier::where('is_active', true)->limit(10)->get();
            
            if ($suppliers->isEmpty()) {
                $message = "Belum ada supplier terdaftar di sistem. 📋\n\n💡 Tambahkan supplier untuk memudahkan manajemen pembelian.";
                return $this->generateResponse('info', $message, ['type' => 'list_suppliers_empty']);
            }
            
            $message = "🏭 **Daftar Supplier Anda** (" . $suppliers->count() . " supplier)\n\n";
            $no = 1;
            foreach ($suppliers as $supplier) {
                $message .= "{$no}. {$supplier->name}\n";
                $message .= "   • Kontak: {$supplier->phone}\n";
                $message .= "   • Email: {$supplier->email}\n";
                if ($supplier->address) {
                    $message .= "   • Alamat: {$supplier->address}\n";
                }
                $message .= "\n";
                $no++;
            }
            
            $message .= "Apakah Anda ingin membuat PO ke salah satu supplier?";
            return $this->generateResponse('data', $message, ['type' => 'list_suppliers']);
        } catch (\Exception $e) {
            // Fallback if Supplier model doesn't exist or table not available
            $message = "🏭 **Daftar Supplier**\n\nFitur supplier sedang dalam pengembangan. Silakan gunakan menu Supplier untuk mengelola data supplier Anda.";
            return $this->generateResponse('info', $message, ['type' => 'list_suppliers_unavailable']);
        }
    }

    private function handleCreatePurchaseOrder(array $entities): array
    {
        $message = "📋 **Purchase Order Baru**\n\nSilakan berikan detail:\n• Supplier\n• Produk\n• Jumlah\n\nContoh: 'Buat PO ke PT Maju Jaya, Bahan Baku 100 unit'";
        return $this->generateResponse('question', $message, ['type' => 'create_purchase_order']);
    }

    // ===== HR HANDLERS =====

    private function handleHR(string $intent, array $entities): array
    {
        return match ($intent) {
            'hr.list_employees' => $this->handleListEmployees($entities),
            'hr.employee_attendance' => $this->handleEmployeeAttendance($entities),
            default => $this->handleUnknown($intent),
        };
    }

    private function handleListEmployees(array $entities): array
    {
        // Check if User model can serve as employees data
        $totalUsers = \App\Models\User::count();
        $activeUsers = \App\Models\User::whereNull('deleted_at')->count();
        
        $message = "👥 **Daftar Pengguna Sistem**\n\n";
        $message .= "Total Pengguna: {$totalUsers} orang\n";
        $message .= "• Aktif: {$activeUsers} orang\n\n";
        $message .= "💡 **Info**: Untuk manajemen karyawan lengkap, gunakan menu Settings > Users.\n\n";
        $message .= "Apakah Anda ingin melihat detail pengguna tertentu?";
        
        return $this->generateResponse('data', $message, ['type' => 'list_employees']);
    }

    private function handleEmployeeAttendance(array $entities): array
    {
        $message = "📊 **Absensi**\n\nFitur absensi dan manajemen karyawan sedang dalam pengembangan.\n\n💡 **Saran**: Gunakan aplikasi HRD terpisah untuk tracking absensi karyawan.\n\nUntuk manajemen pengguna sistem, silakan gunakan menu Settings > Users.";
        return $this->generateResponse('info', $message, ['type' => 'employee_attendance']);
    }

    // ===== MARKETING HANDLERS =====

    private function handleMarketing(string $intent, array $entities): array
    {
        return match ($intent) {
            'marketing.create_campaign' => $this->handleCreateCampaign($entities),
            'marketing.campaign_performance' => $this->handleCampaignPerformance($entities),
            default => $this->handleUnknown($intent),
        };
    }

    private function handleCreateCampaign(array $entities): array
    {
        $message = "📢 **Kampanye Marketing**\n\nFitur kampanye marketing sedang dalam pengembangan.\n\n💡 **Alternatif**:\n• Gunakan fitur Storefront untuk membuat landing page promo\n• Broadcast pesan ke pelanggan via menu Customer Relations\n• Kelola marketplace links di menu Storefront\n\nApakah Anda memerlukan bantuan dengan fitur-fitur ini?";
        return $this->generateResponse('info', $message, ['type' => 'create_campaign']);
    }

    private function handleCampaignPerformance(array $entities): array
    {
        $message = "📊 **Performa Marketing**\n\nFitur tracking campaign performance sedang dalam pengembangan.\n\n💡 **Saat Ini Tersedia**:\n• Lihat statistik penjualan: 'Berapa penjualan hari ini?'\n• Analisis produk terlaris: 'Produk apa yang paling laris?'\n• Trend penjualan: 'Bagaimana trend minggu ini?'\n• Perbandingan bulanan: 'Perbandingan bulan ini vs bulan lalu'\n\nApakah Anda ingin melihat salah satu statistik di atas?";
        return $this->generateResponse('info', $message, ['type' => 'campaign_performance']);
    }

    // ===== REPORTS HANDLERS =====

    private function handleReports(string $intent, array $entities): array
    {
        return match ($intent) {
            'reports.generate_report' => $this->handleGenerateReport($entities),
            default => $this->handleUnknown($intent),
        };
    }

    private function handleGenerateReport(array $entities): array
    {
        $message = "📋 **Laporan Tersedia**\n\n1. 📊 Laporan Penjualan\n2. 📦 Laporan Inventori\n3. 💰 Laporan Keuangan\n4. 👥 Laporan Pelanggan\n5. 👨‍💼 Laporan Karyawan\n\nPilih laporan yang ingin Anda buat atau download.";
        return $this->generateResponse('data', $message, ['type' => 'generate_report']);
    }

    // ===== UTILITY METHODS =====

    private function handleUnknown(string $intent): array
    {
        try {
            // Try to extract keywords and provide relevant data first
            $keywordData = $this->extractAndProvideKeywordData($intent);
            if ($keywordData) {
                return $keywordData;
            }
        } catch (\Exception $e) {
            Log::warning("DEBY AI Keyword extraction failed", [
                'intent' => $intent,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            // Try FAQ search as fallback
            $faqResults = $this->knowledgeBase->searchFAQ($intent);
            if (!empty($faqResults)) {
                $topFAQ = reset($faqResults);
                $answer = $topFAQ['faq']['answer'] ?? 'Maaf, tidak ada jawaban yang tersedia.';
                $this->context->addToHistory('assistant', $answer);
                return $this->generateResponse('faq', $answer, ['type' => 'faq_search']);
            }
        } catch (\Exception $e) {
            Log::warning("DEBY AI FAQ search failed", [
                'intent' => $intent,
                'error' => $e->getMessage(),
            ]);
        }

        // Default message if everything fails
        $message = "Maaf, saya belum sepenuhnya memahami pertanyaan Anda. 😊\n\n💡 Namun, saya akan berusaha menambahkan konteks ini ke pengetahuan saya untuk membantu Anda lebih baik di masa depan.\n\nCoba tanyakan dengan cara lain atau ketik 'bantuan' untuk melihat fitur apa saja yang bisa saya bantu.";
        $this->context->addToHistory('assistant', $message);
        return $this->generateResponse('unknown', $message, ['escalation_needed' => true]);
    }

    /**
     * Extract keywords from input and try to provide relevant data
     */
    private function extractAndProvideKeywordData(string $userInput): ?array
    {
        $userInput = strtolower($userInput);
        
        try {
            // Check for low stock alert keywords (must be before general stock keywords)
            if ($this->containsKeywords($userInput, ['stok rendah', 'stok habis', 'alert stok', 'warning stok', 'kritis', 'produk mana stok rendah'])) {
                return $this->handleLowStockAlert([]);
            }
            
            // Check for revenue/income related keywords
            if ($this->containsKeywords($userInput, ['pendapatan', 'pemasukan', 'penjualan', 'revenue', 'omset', 'income', 'berapa pendapatan', 'berapa pemasukan'])) {
                return $this->handleCheckRevenue([]);
            }
            
            // Check for expense related keywords
            if ($this->containsKeywords($userInput, ['pengeluaran', 'expense', 'biaya', 'bayar', 'belanja', 'berapa pengeluaran'])) {
                return $this->handleExpenseList([]);
            }
            
            // Check for transaction/sales keywords
            if ($this->containsKeywords($userInput, ['transaksi', 'sales', 'lihat transaksi', 'daftar penjualan', 'daftar transaksi'])) {
                return $this->handleSalesList([]);
            }
            
            // Check for customer related keywords
            if ($this->containsKeywords($userInput, ['pelanggan', 'customer', 'berapa pelanggan', 'jumlah pelanggan', 'lihat pelanggan'])) {
                return $this->handleCustomerList([]);
            }
            
            // Check for product related keywords (after customer to avoid conflicts)
            if ($this->containsKeywords($userInput, ['produk', 'product', 'daftar produk', 'tunjukkan produk', 'lihat produk', 'katalog'])) {
                return $this->handleProductList([]);
            }
            
            // Check for stock related keywords (general, after specific ones)
            if ($this->containsKeywords($userInput, ['stok', 'stock', 'inventory', 'posisi stok', 'bagaimana stok'])) {
                return $this->handleProductList([]);
            }
            
            // Check for profit/loss keywords
            if ($this->containsKeywords($userInput, ['laba', 'rugi', 'keuntungan', 'profit', 'margin', 'berapa laba'])) {
                return $this->handleProfitLoss([]);
            }
        } catch (\Exception $e) {
            Log::warning("DEBY AI Keyword data extraction error", [
                'input' => $userInput,
                'error' => $e->getMessage(),
            ]);
        }
        
        return null;
    }

    /**
     * Check if input contains any of the keywords
     */
    private function containsKeywords(string $input, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (strpos($input, strtolower($keyword)) !== false) {
                return true;
            }
        }
        return false;
    }

    private function generateResponse(string $type, string $message, array $metadata = []): array
    {
        $this->context->addToHistory('assistant', $message);

        return [
            'success' => true,
            'type' => $type,
            'message' => $message,
            'metadata' => $metadata,
        ];
    }

    public function getContext(): ConversationContext
    {
        return $this->context;
    }

    // ===== INSIGHT GENERATION METHODS =====

    private function generateRevenueInsight(array $revenueData, float $change): string
    {
        $total = $revenueData['total'];
        $transactions = $revenueData['transactions'];

        if ($total == 0) {
            return "Belum ada penjualan hari ini. Tingkatkan promosi untuk meningkatkan penjualan! 🚀";
        }

        if ($change >= 20) {
            return "Wow! Penjualan meningkat signifikan! Pertahankan momentum ini dengan terus memberikan pelayanan terbaik. 🎉";
        } elseif ($change >= 0) {
            return "Penjualan meningkat dibanding kemarin. Terus jaga konsistensi dan tingkatkan engagement dengan pelanggan. 💪";
        } elseif ($change >= -20) {
            return "Penjualan sedikit menurun. Coba analisis produk mana yang kurang laku dan pertimbangkan strategi promosi. 📊";
        } else {
            return "Penjualan menurun signifikan. Segera tinjau strategi penjualan dan tingkatkan promosi untuk recovery. ⚠️";
        }
    }

    private function generateExpenseInsight(array $expensesData, float $totalExpense, float $revenue = 0): string
    {
        if ($revenue == 0) {
            $revenue = $this->contextManager->getTodayRevenue()['total'];
        }
        $expenseRatio = $revenue > 0 ? ($totalExpense / $revenue) * 100 : 0;

        if ($totalExpense == 0) {
            return "Tidak ada pengeluaran hari ini. Pertahankan efisiensi biaya! ✨";
        }

        if ($expenseRatio > 50) {
            return "Pengeluaran cukup tinggi (" . number_format($expenseRatio, 1) . "% dari revenue). Pertimbangkan untuk mengoptimalkan biaya operasional. 💰";
        } elseif ($expenseRatio > 30) {
            return "Pengeluaran dalam batas normal (" . number_format($expenseRatio, 1) . "%). Tetap monitor untuk memastikan profitabilitas. 📈";
        } else {
            return "Pengeluaran efisien (" . number_format($expenseRatio, 1) . "%)! Margin keuntungan Anda terjaga dengan baik. 🎯";
        }
    }

    private function generateProfitInsight(array $profitData): string
    {
        $profit = $profitData['total'];
        $margin = $profitData['margin'];
        $status = $profitData['status'];

        if ($profit == 0) {
            return "Pendapatan sama dengan pengeluaran. Tidak ada keuntungan atau kerugian hari ini. ⚖️";
        } elseif ($profit > 0) {
            if ($margin >= 50) {
                return "Excellent! Margin keuntungan sangat tinggi ({$margin}%). Bisnis Anda sangat menguntungkan! 🚀";
            } elseif ($margin >= 30) {
                return "Margin keuntungan baik ({$margin}%). Pertahankan strategi bisnis saat ini. 💪";
            } else {
                return "Margin keuntungan cukup ({$margin}%). Coba optimalkan biaya untuk meningkatkan profitabilitas. 📈";
            }
        } else {
            return "Anda mengalami kerugian hari ini. Segera analisis penyebab dan buat strategi perbaikan. ⚠️";
        }
    }

    private function generateStockInsight(array $stockSummary): string
    {
        $lowStock = $stockSummary['low'];
        $limitedStock = $stockSummary['limited'];

        if ($lowStock > 0) {
            return "⚠️ Ada {$lowStock} produk dengan stok rendah. Segera lakukan restock untuk menghindari kehabisan stok! 📦";
        } elseif ($limitedStock > 0) {
            return "Beberapa produk memiliki stok terbatas. Monitor penjualan dan rencanakan restock dalam waktu dekat. 👀";
        } else {
            return "Semua produk memiliki stok yang cukup. Posisi stok Anda sangat baik! ✅";
        }
    }
}
