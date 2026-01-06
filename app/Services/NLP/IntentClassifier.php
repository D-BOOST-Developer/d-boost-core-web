<?php

namespace App\Services\NLP;

class IntentClassifier
{
    private array $intentPatterns = [
        // ===== GREETING & CONVERSATION =====
        'greeting.hello' => [
            'keywords' => ['halo', 'hai', 'hello', 'pagi', 'siang', 'sore', 'malam', 'assalamualaikum', 'assalamu alaikum', 'apa kabar', 'gimana kabar'],
            'patterns' => ['/^(halo|hai|hello|pagi|siang|sore|malam|assalamualaikum)/i'],
            'category' => 'greeting',
        ],
        'greeting.introduction' => [
            'keywords' => ['siapa nama', 'nama kamu', 'siapa kamu', 'perkenalan', 'introduce yourself'],
            'patterns' => ['/siapa\s+(nama|kamu)/i', '/nama\s+kamu/i'],
            'category' => 'greeting',
        ],
        'greeting.affirmation' => [
            'keywords' => ['terima kasih', 'thanks', 'makasih', 'thank you', 'bagus', 'baik', 'ok', 'oke', 'mantap', 'sempurna'],
            'patterns' => ['/terima\s+kasih|thanks|thank\s+you/i'],
            'category' => 'greeting',
        ],
        'greeting.help_request' => [
            'keywords' => ['bantuan', 'help', 'tolong', 'bisa apa', 'apa yang bisa', 'fitur apa'],
            'patterns' => ['/bantuan|help|tolong/i', '/apa\s+yang\s+bisa/i'],
            'category' => 'greeting',
        ],

        // ===== EMOTIONAL & CASUAL =====
        'casual.complaint' => [
            'keywords' => ['masalah', 'error', 'bug', 'tidak bisa', 'tidak jalan', 'rusak', 'error', 'kendala', 'problem'],
            'patterns' => ['/masalah|error|bug|tidak\s+bisa|tidak\s+jalan/i'],
            'category' => 'casual',
        ],
        'casual.sharing' => [
            'keywords' => ['curhat', 'cerita', 'sharing', 'menurut', 'pikiran', 'pendapat', 'bagaimana menurut'],
            'patterns' => ['/curhat|cerita|sharing/i'],
            'category' => 'casual',
        ],

        // ===== INVENTORY INTENTS =====
        'inventory.check_stock' => [
            'keywords' => ['stok', 'stock', 'berapa stok', 'cek stok', 'lihat stok', 'tersedia', 'ada berapa', 'inventory'],
            'patterns' => ['/berapa\s+stok|stok\s+berapa|cek\s+stok|ada\s+berapa/i'],
            'category' => 'inventory',
        ],
        'inventory.update_stock' => [
            'keywords' => ['update stok', 'tambah stok', 'kurangi stok', 'restock', 'stok masuk', 'stok keluar'],
            'patterns' => ['/update\s+stok|tambah\s+stok|kurangi\s+stok|restock/i'],
            'category' => 'inventory',
        ],
        'inventory.add_product' => [
            'keywords' => ['tambah produk', 'produk baru', 'daftar produk', 'buat produk', 'input produk'],
            'patterns' => ['/tambah\s+produk|produk\s+baru|add\s+product/i'],
            'category' => 'inventory',
        ],
        'inventory.low_stock_alert' => [
            'keywords' => ['stok rendah', 'stok habis', 'stok mau habis', 'warning stok', 'alert stok', 'produk mana stok rendah', 'produk stok rendah', 'stok kritis'],
            'patterns' => ['/stok\s+rendah|stok\s+habis|low\s+stock|produk\s+.*\s+stok\s+rendah/i'],
            'category' => 'inventory',
        ],
        'inventory.product_list' => [
            'keywords' => ['daftar produk', 'list produk', 'semua produk', 'produk apa saja', 'katalog', 'tunjukkan produk', 'lihat produk'],
            'patterns' => ['/daftar\s+produk|list\s+produk|semua\s+produk|tunjukkan\s+produk|lihat\s+produk/i'],
            'category' => 'inventory',
        ],
        'inventory.product_search' => [
            'keywords' => ['cari produk', 'search produk', 'produk mana', 'ada produk'],
            'patterns' => ['/cari\s+produk|search\s+produk/i'],
            'category' => 'inventory',
        ],

        // ===== FINANCE INTENTS =====
        'finance.log_expense' => [
            'keywords' => ['catat pengeluaran', 'expense', 'biaya', 'pengeluaran', 'bayar', 'belanja'],
            'patterns' => ['/catat\s+pengeluaran|expense|biaya/i'],
            'category' => 'finance',
        ],
        'finance.check_revenue' => [
            'keywords' => ['pendapatan', 'revenue', 'penjualan', 'omset', 'berapa penjualan', 'total penjualan', 'pemasukan', 'berapa pendapatan', 'berapa pemasukan', 'lihat pendapatan', 'lihat pemasukan'],
            'patterns' => ['/berapa\s+(pendapatan|pemasukan|penjualan)|total\s+penjualan|revenue|omset|lihat\s+(pendapatan|pemasukan)/i'],
            'category' => 'finance',
        ],
        'finance.calculate_profit_loss' => [
            'keywords' => ['laba', 'rugi', 'keuntungan', 'profit', 'loss', 'margin', 'laba rugi', 'berapa laba'],
            'patterns' => ['/berapa\s+laba|laba\s+rugi|profit\s+loss|keuntungan/i'],
            'category' => 'finance',
        ],
        'finance.expense_list' => [
            'keywords' => ['daftar pengeluaran', 'list expense', 'semua pengeluaran', 'expense apa saja', 'pengeluaran', 'berapa pengeluaran', 'lihat pengeluaran'],
            'patterns' => ['/daftar\s+pengeluaran|list\s+expense|semua\s+pengeluaran|berapa\s+pengeluaran|lihat\s+pengeluaran/i'],
            'category' => 'finance',
        ],
        'finance.sales_list' => [
            'keywords' => ['daftar penjualan', 'list penjualan', 'semua penjualan', 'penjualan apa saja', 'transaksi', 'lihat transaksi', 'daftar transaksi'],
            'patterns' => ['/daftar\s+penjualan|list\s+penjualan|semua\s+penjualan|transaksi|lihat\s+transaksi/i'],
            'category' => 'finance',
        ],

        // ===== ANALYTICS INTENTS =====
        'analytics.daily_sales' => [
            'keywords' => ['penjualan hari ini', 'daily sales', 'penjualan harian', 'omset hari ini', 'penjualan hari', 'omset'],
            'patterns' => ['/penjualan\s+hari\s+ini|daily\s+sales|penjualan\s+harian|omset\s+hari\s+ini/i'],
            'category' => 'analytics',
        ],
        'analytics.top_products' => [
            'keywords' => ['produk terlaris', 'top products', 'best seller', 'produk populer', 'paling laku'],
            'patterns' => ['/produk\s+terlaris|top\s+products|best\s+seller/i'],
            'category' => 'analytics',
        ],
        'analytics.weekly_trend' => [
            'keywords' => ['tren minggu', 'weekly trend', 'tren penjualan', 'minggu ini'],
            'patterns' => ['/tren\s+minggu|weekly\s+trend|minggu\s+ini/i'],
            'category' => 'analytics',
        ],
        'analytics.monthly_comparison' => [
            'keywords' => ['perbandingan bulan', 'monthly comparison', 'bulan lalu', 'bulan ini'],
            'patterns' => ['/perbandingan\s+bulan|monthly\s+comparison|bulan\s+lalu/i'],
            'category' => 'analytics',
        ],
        'analytics.performance' => [
            'keywords' => ['performa', 'performance', 'kinerja', 'bagaimana performa', 'bagaimana kinerja'],
            'patterns' => ['/performa|performance|kinerja/i'],
            'category' => 'analytics',
        ],

        // ===== CRM INTENTS =====
        'crm.search_customer' => [
            'keywords' => ['cari pelanggan', 'search customer', 'cari nama', 'data pelanggan', 'detail pelanggan', 'info pelanggan'],
            'patterns' => ['/cari\s+pelanggan|search\s+customer|cari\s+nama|detail\s+pelanggan/i'],
            'category' => 'crm',
        ],
        'crm.customer_list' => [
            'keywords' => ['daftar pelanggan', 'list pelanggan', 'semua pelanggan', 'pelanggan apa saja', 'berapa pelanggan', 'jumlah pelanggan', 'lihat pelanggan', 'berapa banyak pelanggan', 'banyak pelanggan', 'pelanggan hari ini', 'berapa banyak pelanggan hari ini', 'total pelanggan saat ini', 'total pelanggan', 'semua data pelanggan'],
            'patterns' => ['/^total\s+pelanggan|daftar\s+pelanggan|list\s+pelanggan|semua\s+pelanggan|berapa\s+pelanggan|jumlah\s+pelanggan|lihat\s+pelanggan|berapa\s+banyak\s+pelanggan|banyak\s+pelanggan|pelanggan\s+hari\s+ini|total\s+pelanggan\s+saat/i'],
            'category' => 'crm',
        ],
        'crm.send_broadcast' => [
            'keywords' => ['broadcast', 'kirim pesan', 'send message', 'notifikasi'],
            'patterns' => ['/broadcast|kirim\s+pesan|send\s+message/i'],
            'category' => 'crm',
        ],
        'crm.add_followup' => [
            'keywords' => ['follow up', 'followup', 'tindak lanjut', 'reminder pelanggan'],
            'patterns' => ['/follow\s+up|followup|tindak\s+lanjut/i'],
            'category' => 'crm',
        ],

        // ===== SUPPLIER INTENTS =====
        'supplier.list_suppliers' => [
            'keywords' => ['supplier', 'vendor', 'pemasok', 'daftar supplier', 'list supplier'],
            'patterns' => ['/supplier|vendor|pemasok/i'],
            'category' => 'supplier',
        ],
        'supplier.create_purchase_order' => [
            'keywords' => ['buat po', 'purchase order', 'pesan barang', 'order supplier'],
            'patterns' => ['/buat\s+po|purchase\s+order|pesan\s+barang/i'],
            'category' => 'supplier',
        ],

        // ===== HR INTENTS =====
        'hr.list_employees' => [
            'keywords' => ['karyawan', 'employee', 'staff', 'daftar karyawan', 'list employee'],
            'patterns' => ['/karyawan|employee|staff/i'],
            'category' => 'hr',
        ],
        'hr.employee_attendance' => [
            'keywords' => ['absensi', 'attendance', 'kehadiran', 'cuti', 'izin'],
            'patterns' => ['/absensi|attendance|kehadiran/i'],
            'category' => 'hr',
        ],

        // ===== MARKETING INTENTS =====
        'marketing.create_campaign' => [
            'keywords' => ['kampanye', 'campaign', 'promosi', 'marketing', 'iklan'],
            'patterns' => ['/kampanye|campaign|promosi/i'],
            'category' => 'marketing',
        ],
        'marketing.campaign_performance' => [
            'keywords' => ['performa kampanye', 'campaign performance', 'hasil kampanye'],
            'patterns' => ['/performa\s+kampanye|campaign\s+performance/i'],
            'category' => 'marketing',
        ],

        // ===== REPORTS INTENTS =====
        'reports.generate_report' => [
            'keywords' => ['laporan', 'report', 'buat laporan', 'generate report', 'analisis'],
            'patterns' => ['/laporan|report|analisis/i'],
            'category' => 'reports',
        ],
    ];

    public function classify(string $userInput): array
    {
        $userInput = strtolower(trim($userInput));
        $scores = [];

        foreach ($this->intentPatterns as $intent => $config) {
            $score = $this->calculateIntentScore($userInput, $config);
            $scores[$intent] = $score;
        }

        arsort($scores);

        $primaryIntent = key($scores);
        $primaryScore = reset($scores);
        $alternatives = array_slice($scores, 1, 3, true);

        return [
            'primary_intent' => $primaryIntent,
            'confidence' => min($primaryScore, 1.0),
            'alternatives' => $alternatives,
            'all_scores' => $scores,
        ];
    }

    private function calculateIntentScore(string $userInput, array $config): float
    {
        $score = 0;
        $keywordMatches = 0;
        $patternMatches = 0;

        // Keyword scoring dengan weight berdasarkan panjang keyword
        foreach ($config['keywords'] as $keyword) {
            $keyword = strtolower($keyword);
            // Exact match
            if (strpos($userInput, $keyword) !== false) {
                $keywordMatches++;
                // Longer keywords get higher weight (more specific)
                $keywordWeight = min(strlen($keyword) / 20, 1.0);
                $score += 0.4 + (0.4 * $keywordWeight);
            } else {
                // Fuzzy match untuk keywords yang mirip
                $similarity = $this->calculateSimilarity($userInput, $keyword);
                if ($similarity >= 0.75) {
                    $keywordMatches++;
                    $score += (0.3 * $similarity); // Lower score untuk fuzzy match
                }
            }
        }

        // Pattern scoring dengan higher weight
        foreach ($config['patterns'] as $pattern) {
            if (preg_match($pattern, $userInput)) {
                $patternMatches++;
                $score += 0.9;
            }
        }

        // Bonuses untuk multiple matches
        if ($keywordMatches >= 2) {
            $score += 0.4;
        }
        if ($patternMatches >= 1) {
            $score += 0.3;
        }
        if ($keywordMatches >= 1 && $patternMatches >= 1) {
            $score += 0.2; // Bonus untuk kombinasi keyword dan pattern
        }

        return min($score, 1.0);
    }

    /**
     * Calculate similarity between two strings using Levenshtein distance
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        $maxLen = max($len1, $len2);

        if ($maxLen === 0) {
            return 1.0;
        }

        $distance = levenshtein($str1, $str2);
        return 1.0 - ($distance / $maxLen);
    }

    public function getIntentMetadata(string $intent): array
    {
        return $this->intentPatterns[$intent] ?? [
            'category' => 'help',
        ];
    }
}
