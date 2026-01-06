<?php

namespace App\Services\NLP;

class ValidationEngine
{
    /**
     * Validation result
     */
    public array $errors = [];
    public array $warnings = [];

    /**
     * Validate business data
     */
    public function validate(string $intent, array $entities, array $context = []): array
    {
        $this->errors = [];
        $this->warnings = [];

        // Route to specific validator based on intent
        $method = 'validate' . str_replace('_', '', ucwords($intent, '_'));
        
        if (method_exists($this, $method)) {
            $this->$method($entities, $context);
        }

        return [
            'is_valid' => empty($this->errors),
            'errors' => $this->errors,
            'warnings' => $this->warnings,
        ];
    }

    /**
     * Validate product addition
     */
    private function validateInventoryAddProduct(array $entities, array $context): void
    {
        // Product name is required
        if (empty($entities['product_name'])) {
            $this->errors['product_name'] = 'Nama produk harus diisi.';
        }

        // Price validation
        if (!empty($entities['price'])) {
            if ($entities['price'] < 0) {
                $this->errors['price'] = 'Harga tidak boleh negatif.';
            }
            if ($entities['price'] < 1000) {
                $this->warnings['price'] = 'Harga sangat rendah, pastikan sudah benar.';
            }
        }

        // Quantity validation
        if (!empty($entities['quantity'])) {
            if ($entities['quantity'] < 0) {
                $this->errors['quantity'] = 'Stok tidak boleh negatif.';
            }
        }
    }

    /**
     * Validate stock update
     */
    private function validateInventoryUpdateStock(array $entities, array $context): void
    {
        // Product name is required
        if (empty($entities['product_name'])) {
            $this->errors['product_name'] = 'Nama produk harus diisi.';
        }

        // Quantity is required
        if (empty($entities['quantity'])) {
            $this->errors['quantity'] = 'Jumlah stok harus diisi.';
        } else {
            if ($entities['quantity'] < 0) {
                $this->errors['quantity'] = 'Stok tidak boleh negatif.';
            }
        }
    }

    /**
     * Validate expense logging
     */
    private function validateFinanceLogExpense(array $entities, array $context): void
    {
        // Price (amount) is required
        if (empty($entities['price'])) {
            $this->errors['price'] = 'Jumlah pengeluaran harus diisi.';
        } else {
            if ($entities['price'] < 0) {
                $this->errors['price'] = 'Pengeluaran tidak boleh negatif.';
            }
        }

        // Category is recommended
        if (empty($entities['expense_category'])) {
            $this->warnings['expense_category'] = 'Sebaiknya tentukan kategori pengeluaran.';
        }
    }

    /**
     * Validate price setting
     */
    private function validateOnboardingSetPrice(array $entities, array $context): void
    {
        // Price is required
        if (empty($entities['price'])) {
            $this->errors['price'] = 'Harga harus diisi.';
        } else {
            if ($entities['price'] < 0) {
                $this->errors['price'] = 'Harga tidak boleh negatif.';
            }
            if ($entities['price'] < 1000) {
                $this->warnings['price'] = 'Harga sangat rendah, pastikan sudah benar.';
            }
        }
    }

    /**
     * Validate stock input
     */
    private function validateOnboardingInputStock(array $entities, array $context): void
    {
        // Quantity is required
        if (empty($entities['quantity'])) {
            $this->errors['quantity'] = 'Jumlah stok harus diisi.';
        } else {
            if ($entities['quantity'] < 0) {
                $this->errors['quantity'] = 'Stok tidak boleh negatif.';
            }
        }
    }

    /**
     * Validate customer search
     */
    private function validateCrmSearchCustomer(array $entities, array $context): void
    {
        // Customer name is required
        if (empty($entities['customer_name'])) {
            $this->errors['customer_name'] = 'Nama pelanggan harus diisi.';
        }
    }

    /**
     * Validate broadcast (requires confirmation)
     */
    private function validateCrmSendBroadcast(array $entities, array $context): void
    {
        // This action requires confirmation
        $this->warnings['confirmation_required'] = 'Broadcast akan dikirim ke semua pelanggan. Pastikan pesan sudah benar.';
    }

    /**
     * Check if price is below cost (business rule)
     */
    public function validatePriceAboveCost(int $price, int $costPrice): array
    {
        if ($price < $costPrice) {
            return [
                'is_valid' => false,
                'error' => 'Harga jual tidak boleh lebih rendah dari harga modal.',
                'suggestion' => "Harga modal: Rp " . number_format($costPrice, 0, ',', '.') . 
                               ". Harga jual minimal: Rp " . number_format($costPrice, 0, ',', '.'),
            ];
        }

        return ['is_valid' => true];
    }

    /**
     * Check profit margin
     */
    public function validateProfitMargin(int $price, int $costPrice, int $minMarginPercent = 20): array
    {
        $margin = (($price - $costPrice) / $costPrice) * 100;

        if ($margin < $minMarginPercent) {
            return [
                'is_valid' => false,
                'warning' => "Margin keuntungan hanya {$margin}%, kurang dari target {$minMarginPercent}%.",
                'suggestion' => "Pertimbangkan naikkan harga ke minimal Rp " . 
                               number_format($costPrice * (1 + $minMarginPercent / 100), 0, ',', '.'),
            ];
        }

        return ['is_valid' => true];
    }

    /**
     * Validate numeric input
     */
    public function validateNumeric($value, string $fieldName): array
    {
        if (!is_numeric($value)) {
            return [
                'is_valid' => false,
                'error' => "{$fieldName} harus berupa angka.",
            ];
        }

        return ['is_valid' => true];
    }

    /**
     * Validate date format
     */
    public function validateDateFormat(string $date): array
    {
        if (!strtotime($date)) {
            return [
                'is_valid' => false,
                'error' => 'Format tanggal tidak valid. Gunakan format: YYYY-MM-DD atau DD/MM/YYYY.',
            ];
        }

        return ['is_valid' => true];
    }

    /**
     * Validate date range
     */
    public function validateDateRange(string $startDate, string $endDate): array
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);

        if ($start > $end) {
            return [
                'is_valid' => false,
                'error' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.',
            ];
        }

        return ['is_valid' => true];
    }

    /**
     * Get validation error message
     */
    public function getErrorMessage(): string
    {
        if (empty($this->errors)) {
            return '';
        }

        $messages = array_values($this->errors);
        return implode(' ', $messages);
    }

    /**
     * Get validation warning message
     */
    public function getWarningMessage(): string
    {
        if (empty($this->warnings)) {
            return '';
        }

        $messages = array_values($this->warnings);
        return implode(' ', $messages);
    }
}
