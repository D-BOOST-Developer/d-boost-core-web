<?php

namespace App\Services\NLP;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class DebyAIEngine
{
    private IntentClassifier $intentClassifier;
    private EntityExtractor $entityExtractor;
    private ValidationEngine $validationEngine;
    private KnowledgeBase $knowledgeBase;
    private ConversationContext $context;
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->intentClassifier = new IntentClassifier();
        $this->entityExtractor = new EntityExtractor();
        $this->validationEngine = new ValidationEngine();
        $this->knowledgeBase = new KnowledgeBase();
        $this->context = new ConversationContext($user->id);
    }

    /**
     * Process user input and generate response
     */
    public function process(string $userInput): array
    {
        // Add to conversation history
        $this->context->addToHistory('user', $userInput);

        // Step 1: Classify intent
        $intentResult = $this->intentClassifier->classify($userInput);
        $intent = $intentResult['primary_intent'];
        $confidence = $intentResult['confidence'];

        Log::info("DEBY AI Intent Classification", [
            'user_id' => $this->user->id,
            'intent' => $intent,
            'confidence' => $confidence,
        ]);

        // Step 2: Extract entities
        $entities = $this->entityExtractor->extract($userInput);

        // Step 3: Get intent metadata
        $intentMetadata = $this->intentClassifier->getIntentMetadata($intent);

        // Step 4: Handle low confidence - increase threshold
        if ($confidence < 0.3) {
            return $this->handleLowConfidence($intentResult, $userInput);
        }

        // Step 5: Route to appropriate handler
        return $this->routeToHandler($intent, $entities, $intentMetadata);
    }

    /**
     * Route to appropriate handler based on intent
     */
    private function routeToHandler(string $intent, array $entities, array $metadata): array
    {
        $category = $metadata['category'];

        // Check if form filling is needed
        if ($metadata['requires_slot_filling']) {
            return $this->handleFormFilling($intent, $entities, $metadata);
        }

        // Check if confirmation is needed
        if ($metadata['requires_confirmation']) {
            return $this->handleConfirmation($intent, $entities, $metadata);
        }

        // Route by category
        return match ($category) {
            'onboarding' => $this->handleOnboarding($intent, $entities),
            'inventory' => $this->handleInventory($intent, $entities),
            'finance' => $this->handleFinance($intent, $entities),
            'analytics' => $this->handleAnalytics($intent, $entities),
            'crm' => $this->handleCRM($intent, $entities),
            'help' => $this->handleHelp($intent, $entities),
            default => $this->handleUnknown($intent),
        };
    }

    /**
     * Handle form filling for intents that need slot filling
     */
    private function handleFormFilling(string $intent, array $entities, array $metadata): array
    {
        $currentState = $this->context->getState();

        // Determine required slots based on intent
        $requiredSlots = match ($intent) {
            'inventory.add_product' => ['product_name', 'price', 'quantity', 'category'],
            'finance.log_expense' => ['price', 'expense_category'],
            'crm.add_followup' => ['customer_name', 'followup_date'],
            default => [],
        };

        // Check which slots are missing
        $missingSlots = [];
        foreach ($requiredSlots as $slot) {
            if (empty($entities[$slot])) {
                $missingSlots[] = $slot;
            }
        }

        // If all slots are filled, proceed with action
        if (empty($missingSlots)) {
            return $this->executeAction($intent, $entities);
        }

        // Ask for missing slots
        $nextSlot = reset($missingSlots);
        $message = $this->getSlotFillingPrompt($intent, $nextSlot);

        $this->context->setWaitingForSlot($intent, $nextSlot, $entities);

        return $this->generateResponse(
            'slot_filling',
            $message,
            ['missing_slots' => $missingSlots, 'next_slot' => $nextSlot]
        );
    }

    /**
     * Handle confirmation
     */
    private function handleConfirmation(string $intent, array $entities, array $metadata): array
    {
        $pendingAction = $this->context->getPendingAction();

        if ($pendingAction && $pendingAction['action'] === $intent) {
            return $this->generateResponse(
                'confirmation_needed',
                'Apakah Anda yakin ingin melanjutkan?',
                ['action' => $pendingAction['action']]
            );
        }

        // Set waiting for confirmation
        $confirmationMessage = match ($intent) {
            'crm.send_broadcast' => 'Broadcast akan dikirim ke semua pelanggan. Pastikan pesan sudah benar. Lanjutkan?',
            default => 'Apakah Anda yakin ingin melanjutkan?',
        };

        $this->context->setWaitingForConfirmation($intent, $entities);

        return $this->generateResponse(
            'confirmation_needed',
            $confirmationMessage,
            ['action' => $intent]
        );
    }

    /**
     * Handle onboarding intents
     */
    private function handleOnboarding(string $intent, array $entities): array
    {
        $faq = $this->knowledgeBase->getFAQByIntent($intent);

        if (!$faq) {
            return $this->handleUnknown($intent);
        }

        $this->context->addToHistory('assistant', $faq['answer']);

        return $this->generateResponse(
            'tutorial',
            $faq['answer'],
            [
                'intent' => $intent,
                'type' => 'tutorial',
                'next_action' => $this->getNextOnboardingStep($intent),
            ]
        );
    }

    /**
     * Handle inventory intents
     */
    private function handleInventory(string $intent, array $entities): array
    {
        return match ($intent) {
            'inventory.check_stock' => $this->handleCheckStock($entities),
            'inventory.update_stock' => $this->handleUpdateStock($entities),
            'inventory.add_product' => $this->handleAddProduct($entities),
            'inventory.low_stock_alert' => $this->handleLowStockAlert($entities),
            'inventory.restock_reminder' => $this->handleRestockReminder($entities),
            default => $this->handleUnknown($intent),
        };
    }

    /**
     * Handle finance intents
     */
    private function handleFinance(string $intent, array $entities): array
    {
        return match ($intent) {
            'finance.log_expense' => $this->handleLogExpense($entities),
            'finance.check_revenue' => $this->handleCheckRevenue($entities),
            'finance.calculate_profit_loss' => $this->handleProfitLoss($entities),
            'finance.export_report' => $this->handleExportReport($entities),
            default => $this->handleUnknown($intent),
        };
    }

    /**
     * Handle analytics intents
     */
    private function handleAnalytics(string $intent, array $entities): array
    {
        return match ($intent) {
            'analytics.daily_sales' => $this->handleDailySales($entities),
            'analytics.top_products' => $this->handleTopProducts($entities),
            'analytics.weekly_trend' => $this->handleWeeklyTrend($entities),
            'analytics.monthly_comparison' => $this->handleMonthlyComparison($entities),
            default => $this->handleUnknown($intent),
        };
    }

    /**
     * Handle CRM intents
     */
    private function handleCRM(string $intent, array $entities): array
    {
        return match ($intent) {
            'crm.search_customer' => $this->handleSearchCustomer($entities),
            'crm.send_broadcast' => $this->handleSendBroadcast($entities),
            'crm.add_followup' => $this->handleAddFollowup($entities),
            default => $this->handleUnknown($intent),
        };
    }

    /**
     * Handle help intents
     */
    private function handleHelp(string $intent, array $entities): array
    {
        $faq = $this->knowledgeBase->getFAQByIntent($intent);

        if ($faq) {
            $this->context->addToHistory('assistant', $faq['answer']);
            return $this->generateResponse(
                'faq',
                $faq['answer'],
                ['intent' => $intent, 'type' => 'faq']
            );
        }

        return $this->handleUnknown($intent);
    }

    /**
     * Handle low confidence
     */
    private function handleLowConfidence(array $intentResult, string $userInput): array
    {
        $alternatives = $intentResult['alternatives'];

        $message = "Maaf, saya kurang mengerti. Apakah maksud Anda:";

        $suggestions = [];
        foreach ($alternatives as $intent => $score) {
            $suggestions[] = $intent;
        }

        return $this->generateResponse(
            'clarification_needed',
            $message,
            [
                'suggestions' => $suggestions,
                'original_input' => $userInput,
                'confidence' => $intentResult['confidence'],
            ]
        );
    }

    // ===== INVENTORY HANDLERS =====

    private function handleCheckStock(array $entities): array
    {
        $productName = $entities['product_name'] ?? 'produk';
        
        // Simulate realistic stock data
        $stocks = [
            'nasi uduk' => 45,
            'soto ayam' => 32,
            'lumpia' => 28,
            'tahu goreng' => 15,
            'perkedel' => 50,
        ];
        
        $stockQty = $stocks[strtolower($productName)] ?? rand(10, 100);
        $status = $stockQty > 30 ? 'Stok tersedia dengan baik' : ($stockQty > 10 ? 'Stok terbatas' : 'Stok rendah');
        
        $message = "📦 Stok {$productName}: {$stockQty} unit\n{$status}\n\nApakah Anda ingin melakukan pemesanan atau update stok?";
        return $this->generateResponse('action', $message, ['type' => 'check_stock', 'stock' => $stockQty]);
    }

    private function handleUpdateStock(array $entities): array
    {
        $productName = $entities['product_name'] ?? 'produk';
        $quantity = $entities['quantity'] ?? 0;
        $message = "✅ Stok {$productName} akan diperbarui +{$quantity} unit.\n\nKonfirmasi perubahan ini?";
        return $this->generateResponse('confirmation', $message, ['type' => 'update_stock']);
    }

    private function handleAddProduct(array $entities): array
    {
        $productName = $entities['product_name'] ?? 'produk baru';
        $price = $entities['price'] ?? 0;
        $category = $entities['category'] ?? 'umum';
        $quantity = $entities['quantity'] ?? 0;
        
        $message = "📝 Produk Baru:\n• Nama: {$productName}\n• Harga: Rp " . number_format($price, 0) . "\n• Kategori: {$category}\n• Stok Awal: {$quantity} unit\n\nTambahkan produk ini?";
        return $this->generateResponse('confirmation', $message, ['type' => 'add_product']);
    }

    private function handleLowStockAlert(array $entities): array
    {
        $lowStockProducts = [
            'Tahu Goreng' => 8,
            'Perkedel' => 12,
            'Tempe Goreng' => 5,
        ];
        
        $list = "⚠️ Produk dengan Stok Rendah:\n";
        foreach ($lowStockProducts as $product => $qty) {
            $list .= "• {$product}: {$qty} unit\n";
        }
        
        $message = $list . "\nApakah Anda ingin melakukan restock untuk produk-produk ini?";
        return $this->generateResponse('alert', $message, ['type' => 'low_stock_alert']);
    }

    private function handleRestockReminder(array $entities): array
    {
        $message = "🔔 Pengingat Restock Aktif\n\nSaya akan mengingatkan Anda setiap:\n• Hari Jumat pukul 17:00\n• Ketika stok produk di bawah 10 unit\n\nPengaturan ini sudah tersimpan.";
        return $this->generateResponse('action', $message, ['type' => 'restock_reminder']);
    }

    // ===== FINANCE HANDLERS =====

    private function handleLogExpense(array $entities): array
    {
        $amount = $entities['price'] ?? 0;
        $category = $entities['expense_category'] ?? 'umum';
        $date = date('d/m/Y');
        $message = "💰 Pengeluaran Baru:\n• Jumlah: Rp " . number_format($amount, 0) . "\n• Kategori: {$category}\n• Tanggal: {$date}\n\nCatat pengeluaran ini?";
        return $this->generateResponse('confirmation', $message, ['type' => 'log_expense']);
    }

    private function handleCheckRevenue(array $entities): array
    {
        $period = $entities['date_range']['period'] ?? 'bulan ini';
        $revenue = 2500000;
        $transactions = 25;
        $avgTransaction = $revenue / $transactions;
        
        $message = "💵 Pendapatan {$period}:\n• Total: Rp " . number_format($revenue, 0) . "\n• Transaksi: {$transactions}\n• Rata-rata: Rp " . number_format($avgTransaction, 0) . "\n\nTrend: ↑ Naik 12% dibanding periode sebelumnya";
        return $this->generateResponse('data', $message, ['type' => 'revenue_check']);
    }

    private function handleProfitLoss(array $entities): array
    {
        $revenue = 2500000;
        $expenses = 1300000;
        $profit = $revenue - $expenses;
        $margin = ($profit / $revenue) * 100;
        
        $message = "📊 Laporan Laba Rugi Bulan Ini:\n• Pendapatan: Rp " . number_format($revenue, 0) . "\n• Pengeluaran: Rp " . number_format($expenses, 0) . "\n• Laba Bersih: Rp " . number_format($profit, 0) . "\n• Margin: {$margin}%\n\n✅ Kinerja bisnis Anda sangat baik!";
        return $this->generateResponse('data', $message, ['type' => 'profit_loss']);
    }

    private function handleExportReport(array $entities): array
    {
        $message = "📥 Laporan Siap Diunduh\n\nFormat tersedia:\n• Excel (.xlsx)\n• PDF (.pdf)\n• CSV (.csv)\n\nPilih format yang Anda inginkan.";
        return $this->generateResponse('action', $message, ['type' => 'export_report']);
    }

    // ===== ANALYTICS HANDLERS =====

    private function handleDailySales(array $entities): array
    {
        $today = date('d/m/Y');
        $sales = 450000;
        $transactions = 8;
        $avgSale = $sales / $transactions;
        
        $message = "📈 Penjualan Hari Ini ({$today}):\n• Total: Rp " . number_format($sales, 0) . "\n• Transaksi: {$transactions}\n• Rata-rata: Rp " . number_format($avgSale, 0) . "\n\n🏆 Produk Terlaris: Nasi Uduk (5 penjualan)\n\nStatus: Hari yang baik! 😊";
        return $this->generateResponse('data', $message, ['type' => 'daily_sales']);
    }

    private function handleTopProducts(array $entities): array
    {
        $message = "🏆 Top 3 Produk Terlaris:\n\n1️⃣ Nasi Uduk\n   • Penjualan: 150 unit\n   • Revenue: Rp 1.500.000\n\n2️⃣ Soto Ayam\n   • Penjualan: 120 unit\n   • Revenue: Rp 1.200.000\n\n3️⃣ Lumpia\n   • Penjualan: 95 unit\n   • Revenue: Rp 950.000\n\nTotal Revenue Top 3: Rp 3.650.000 (60% dari total)";
        return $this->generateResponse('data', $message, ['type' => 'top_products']);
    }

    private function handleWeeklyTrend(array $entities): array
    {
        $message = "📊 Tren Penjualan Minggu Ini:\n\nSenin: Rp 280.000 ↑\nSelasa: Rp 320.000 ↑\nRabu: Rp 350.000 ↑\nKamis: Rp 380.000 ↑\nJumat: Rp 450.000 ⬆️ (Tertinggi)\nSabtu: Rp 420.000\nMinggu: Rp 400.000\n\n📈 Trend: +15% dibanding minggu lalu\n💡 Insight: Penjualan meningkat menjelang akhir minggu";
        return $this->generateResponse('data', $message, ['type' => 'weekly_trend']);
    }

    private function handleMonthlyComparison(array $entities): array
    {
        $message = "📊 Perbandingan Bulan Ini vs Bulan Lalu:\n\n💰 Penjualan: +25% (Rp 2.5M → Rp 3.1M)\n👥 Pelanggan Baru: +12% (48 → 54 orang)\n💵 Rata-rata Transaksi: +8% (Rp 85K → Rp 92K)\n📦 Produk Terjual: +18% (520 → 615 unit)\n\n✅ Kesimpulan: Bisnis Anda berkembang dengan baik!\n\n🎯 Rekomendasi: Tingkatkan stok produk terlaris";
        return $this->generateResponse('data', $message, ['type' => 'monthly_comparison']);
    }

    // ===== CRM HANDLERS =====

    private function handleSearchCustomer(array $entities): array
    {
        $customerName = $entities['customer_name'] ?? 'pelanggan';
        $totalPurchase = 2500000;
        $transactions = 12;
        $lastTransaction = '3 hari lalu';
        
        $message = "👤 Data Pelanggan: {$customerName}\n\n• Total Pembelian: Rp " . number_format($totalPurchase, 0) . "\n• Jumlah Transaksi: {$transactions}\n• Rata-rata: Rp " . number_format($totalPurchase / $transactions, 0) . "\n• Transaksi Terakhir: {$lastTransaction}\n• Status: Pelanggan Setia ⭐\n\nApakah Anda ingin melihat detail lebih lanjut atau menambah follow-up?";
        return $this->generateResponse('data', $message, ['type' => 'customer_search']);
    }

    private function handleSendBroadcast(array $entities): array
    {
        $totalCustomers = 54;
        $message = "📢 Broadcast Pesan:\n\nPenerima: Semua Pelanggan ({$totalCustomers} orang)\n\nKonfirmasi pengiriman broadcast ini?";
        return $this->generateResponse('confirmation', $message, ['type' => 'send_broadcast']);
    }

    private function handleAddFollowup(array $entities): array
    {
        $customerName = $entities['customer_name'] ?? 'pelanggan';
        $date = $entities['date'] ?? 'besok';
        $message = "📅 Follow-up Baru:\n• Pelanggan: {$customerName}\n• Tanggal: {$date}\n• Waktu: 10:00 AM\n\nBuat follow-up ini?";
        return $this->generateResponse('confirmation', $message, ['type' => 'add_followup']);
    }

    // ===== UTILITY METHODS =====

    private function getSlotFillingPrompt(string $intent, string $slot): string
    {
        return match ($slot) {
            'product_name' => 'Apa nama produk yang ingin Anda tambahkan?',
            'price' => 'Berapa harga produk tersebut?',
            'quantity' => 'Berapa jumlah stok awalnya?',
            'category' => 'Kategori produk apa?',
            'expense_category' => 'Kategori pengeluaran apa?',
            'customer_name' => 'Siapa nama pelanggannya?',
            'followup_date' => 'Kapan Anda ingin melakukan follow-up?',
            default => 'Informasi apa yang Anda ingin tambahkan?',
        };
    }

    private function getNextOnboardingStep(string $intent): string
    {
        return match ($intent) {
            'onboarding.start' => 'onboarding.add_product',
            'onboarding.add_product' => 'onboarding.add_customer',
            'onboarding.add_customer' => 'onboarding.first_sale',
            default => 'onboarding.complete',
        };
    }

    private function executeAction(string $intent, array $entities): array
    {
        $message = match ($intent) {
            'inventory.add_product' => "Produk '{$entities['product_name']}' berhasil ditambahkan!",
            'finance.log_expense' => "Pengeluaran sebesar Rp {$entities['price']} berhasil dicatat!",
            'crm.add_followup' => "Follow-up untuk {$entities['customer_name']} berhasil dibuat!",
            default => 'Aksi berhasil dijalankan!',
        };

        return $this->generateResponse('action_completed', $message, ['type' => $intent]);
    }

    /**
     * Handle unknown intent
     */
    private function handleUnknown(string $intent): array
    {
        // Try to search FAQ
        $faqResults = $this->knowledgeBase->searchFAQ($intent);

        if (!empty($faqResults)) {
            $topFAQ = reset($faqResults);
            $answer = $topFAQ['faq']['answer'];
            $this->context->addToHistory('assistant', $answer);

            return $this->generateResponse(
                'faq',
                $answer,
                ['type' => 'faq_search', 'confidence' => 0.6]
            );
        }

        $message = "Maaf, saya belum memahami pertanyaan Anda. Coba tanyakan dengan cara lain atau hubungi customer service kami.";
        $this->context->addToHistory('assistant', $message);

        return $this->generateResponse(
            'unknown',
            $message,
            [
                'escalation_needed' => true,
                'contact_cs' => true,
            ]
        );
    }

    /**
     * Generate response
     */
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

    /**
     * Get context
     */
    public function getContext(): ConversationContext
    {
        return $this->context;
    }
}
