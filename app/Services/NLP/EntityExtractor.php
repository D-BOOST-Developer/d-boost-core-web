<?php

namespace App\Services\NLP;

use Illuminate\Support\Collection;

class EntityExtractor
{
    /**
     * Extract entities from user input
     */
    public function extract(string $input): array
    {
        $input = strtolower($input);

        return [
            'product_name' => $this->extractProductName($input),
            'quantity' => $this->extractQuantity($input),
            'price' => $this->extractPrice($input),
            'category' => $this->extractCategory($input),
            'customer_name' => $this->extractCustomerName($input),
            'date_range' => $this->extractDateRange($input),
            'marketplace' => $this->extractMarketplace($input),
            'payment_method' => $this->extractPaymentMethod($input),
            'expense_category' => $this->extractExpenseCategory($input),
            'status' => $this->extractStatus($input),
        ];
    }

    /**
     * Extract product name from input
     */
    private function extractProductName(string $input): ?string
    {
        // Pattern: "produk [name]", "nama [name]", "barang [name]"
        $patterns = [
            '/produk\s+(?:bernama\s+)?([a-zA-Z0-9\s]+?)(?:\s+(?:harga|stok|kategori|berapa|dengan)|$)/i',
            '/nama\s+([a-zA-Z0-9\s]+?)(?:\s+(?:harga|stok|kategori|berapa)|$)/i',
            '/barang\s+([a-zA-Z0-9\s]+?)(?:\s+(?:harga|stok|kategori)|$)/i',
            '/(?:tambah|input|daftarkan)\s+(?:produk\s+)?([a-zA-Z0-9\s]+?)(?:\s+(?:harga|stok|kategori|berapa)|$)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    /**
     * Extract quantity/number from input
     */
    private function extractQuantity(string $input): ?int
    {
        // Convert Indonesian number words to digits
        $numberWords = [
            'nol' => 0, 'satu' => 1, 'dua' => 2, 'tiga' => 3, 'empat' => 4,
            'lima' => 5, 'enam' => 6, 'tujuh' => 7, 'delapan' => 8, 'sembilan' => 9,
            'sepuluh' => 10, 'sebelas' => 11, 'dua puluh' => 20, 'tiga puluh' => 30,
            'seratus' => 100, 'seribu' => 1000,
        ];

        // Check for digit patterns
        if (preg_match('/(\d+)\s*(?:buah|pcs|pieces|unit|item|box|karton)?/i', $input, $matches)) {
            return (int) $matches[1];
        }

        // Check for number words
        foreach ($numberWords as $word => $value) {
            if (strpos($input, $word) !== false) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Extract price from input
     */
    private function extractPrice(string $input): ?int
    {
        // Pattern: "10 ribu", "10000", "10k", "Rp 10000"
        $patterns = [
            '/(?:harga|seharga|rp|rupiah)?\s*(\d+)\s*(?:ribu|k|rb)?(?:\s|$)/i',
            '/(\d+)\s*(?:ribu|k|rb)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                $price = (int) $matches[1];
                // If "ribu" or "k" is mentioned, multiply by 1000
                if (preg_match('/ribu|k|rb/i', $matches[0])) {
                    $price *= 1000;
                }
                return $price;
            }
        }

        return null;
    }

    /**
     * Extract category from input
     */
    private function extractCategory(string $input): ?string
    {
        $categories = [
            'makanan' => ['makanan', 'food', 'kuliner', 'kue', 'roti', 'snack'],
            'minuman' => ['minuman', 'drink', 'kopi', 'teh', 'jus', 'air'],
            'fashion' => ['fashion', 'pakaian', 'baju', 'celana', 'sepatu', 'tas'],
            'elektronik' => ['elektronik', 'gadget', 'hp', 'laptop', 'charger'],
            'kecantikan' => ['kecantikan', 'beauty', 'kosmetik', 'skincare', 'makeup'],
            'kesehatan' => ['kesehatan', 'obat', 'vitamin', 'suplemen', 'health'],
            'rumah_tangga' => ['rumah tangga', 'peralatan', 'furniture', 'dekorasi'],
        ];

        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($input, $keyword) !== false) {
                    return $category;
                }
            }
        }

        return null;
    }

    /**
     * Extract customer name from input
     */
    private function extractCustomerName(string $input): ?string
    {
        // Pattern: "pelanggan [name]", "customer [name]", "nama [name]"
        $patterns = [
            '/pelanggan\s+(?:bernama\s+)?([a-zA-Z\s]+?)(?:\s+(?:beli|order|pesan|nomor)|$)/i',
            '/customer\s+([a-zA-Z\s]+?)(?:\s+(?:beli|order)|$)/i',
            '/nama\s+([a-zA-Z\s]+?)(?:\s+(?:pelanggan|customer)|$)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    /**
     * Extract date range from input
     */
    private function extractDateRange(string $input): array
    {
        $result = [
            'start_date' => null,
            'end_date' => null,
            'period' => null,
        ];

        // Check for period keywords
        if (preg_match('/(hari ini|today)/i', $input)) {
            $result['period'] = 'today';
            $result['start_date'] = now()->format('Y-m-d');
            $result['end_date'] = now()->format('Y-m-d');
        } elseif (preg_match('/(minggu ini|this week)/i', $input)) {
            $result['period'] = 'week';
            $result['start_date'] = now()->startOfWeek()->format('Y-m-d');
            $result['end_date'] = now()->endOfWeek()->format('Y-m-d');
        } elseif (preg_match('/(bulan ini|this month)/i', $input)) {
            $result['period'] = 'month';
            $result['start_date'] = now()->startOfMonth()->format('Y-m-d');
            $result['end_date'] = now()->endOfMonth()->format('Y-m-d');
        } elseif (preg_match('/(tahun ini|this year)/i', $input)) {
            $result['period'] = 'year';
            $result['start_date'] = now()->startOfYear()->format('Y-m-d');
            $result['end_date'] = now()->endOfYear()->format('Y-m-d');
        }

        // Check for specific dates
        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/i', $input, $matches)) {
            $result['start_date'] = sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
        }

        return $result;
    }

    /**
     * Extract marketplace from input
     */
    private function extractMarketplace(string $input): ?string
    {
        $marketplaces = [
            'tokopedia' => ['tokopedia', 'toko pedia'],
            'shopee' => ['shopee'],
            'lazada' => ['lazada'],
            'bukalapak' => ['bukalapak', 'buka lapak'],
            'instagram' => ['instagram', 'ig', 'insta'],
            'facebook' => ['facebook', 'fb'],
            'tiktok' => ['tiktok', 'tik tok'],
            'website' => ['website', 'web', 'toko online'],
        ];

        foreach ($marketplaces as $marketplace => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($input, $keyword) !== false) {
                    return $marketplace;
                }
            }
        }

        return null;
    }

    /**
     * Extract payment method from input
     */
    private function extractPaymentMethod(string $input): ?string
    {
        $methods = [
            'cash' => ['tunai', 'cash', 'langsung bayar'],
            'transfer' => ['transfer', 'bank', 'bca', 'mandiri', 'bni'],
            'ewallet' => ['ewallet', 'e-wallet', 'gopay', 'ovo', 'dana', 'linkaja'],
            'credit_card' => ['kartu kredit', 'credit card', 'cc'],
            'installment' => ['cicilan', 'installment', 'kredit'],
        ];

        foreach ($methods as $method => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($input, $keyword) !== false) {
                    return $method;
                }
            }
        }

        return null;
    }

    /**
     * Extract expense category from input
     */
    private function extractExpenseCategory(string $input): ?string
    {
        $categories = [
            'supplies' => ['supplies', 'perlengkapan', 'bahan', 'material'],
            'utilities' => ['listrik', 'air', 'internet', 'telepon', 'utilitas'],
            'rent' => ['sewa', 'rent', 'tempat', 'lokasi'],
            'salary' => ['gaji', 'salary', 'upah', 'honor'],
            'marketing' => ['marketing', 'iklan', 'promosi', 'ads'],
            'transport' => ['transport', 'bensin', 'ongkir', 'pengiriman'],
            'maintenance' => ['maintenance', 'perbaikan', 'service', 'perawatan'],
            'other' => ['lainnya', 'other', 'misc', 'miscellaneous'],
        ];

        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($input, $keyword) !== false) {
                    return $category;
                }
            }
        }

        return null;
    }

    /**
     * Extract status from input
     */
    private function extractStatus(string $input): ?string
    {
        $statuses = [
            'active' => ['aktif', 'active', 'buka'],
            'inactive' => ['tidak aktif', 'inactive', 'tutup', 'nonaktif'],
            'pending' => ['pending', 'menunggu', 'proses'],
            'completed' => ['selesai', 'completed', 'done', 'rampung'],
            'cancelled' => ['batal', 'cancelled', 'cancel'],
        ];

        foreach ($statuses as $status => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($input, $keyword) !== false) {
                    return $status;
                }
            }
        }

        return null;
    }
}
