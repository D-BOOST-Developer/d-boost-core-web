<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'customer_id',
        'sale_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'status',
        'order_source',
        'notes',
        'rejection_reason',
        'customer_snapshot',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'customer_snapshot' => 'array',
        ];
    }

    /**
     * Generate a unique invoice number
     * Uses max invoice number approach to avoid race conditions
     */
    public static function generateInvoiceNumber()
    {
        $maxAttempts = 50;
        $attempts = 0;
        $datePrefix = date('Ymd');

        while ($attempts < $maxAttempts) {
            try {
                // invoice_number is globally UNIQUE, so we must bypass the per-user
                // global scope from BelongsToUser when looking up existing invoices.
                $lastSale = static::withoutGlobalScope('user')
                    ->where('invoice_number', 'like', 'INV-' . $datePrefix . '-%')
                    ->orderByRaw("CAST(SUBSTRING(invoice_number FROM '([0-9]+)$') AS INTEGER) DESC")
                    ->first();

                if ($lastSale) {
                    preg_match('/(\d+)$/', $lastSale->invoice_number, $matches);
                    $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
                    $sequentialNumber = $lastNumber + 1;
                } else {
                    $sequentialNumber = 1;
                }

                $invoiceNumber = 'INV-' . $datePrefix . '-' . str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);

                if (!static::withoutGlobalScope('user')->where('invoice_number', $invoiceNumber)->exists()) {
                    return $invoiceNumber;
                }

                $sequentialNumber++;
                $invoiceNumber = 'INV-' . $datePrefix . '-' . str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);

                if (!static::withoutGlobalScope('user')->where('invoice_number', $invoiceNumber)->exists()) {
                    return $invoiceNumber;
                }

            } catch (\Exception $e) {
                $microtime = explode(' ', microtime());
                $timestamp = $microtime[1];
                $microseconds = str_pad(substr($microtime[0], 2, 6), 6, '0');
                $invoiceNumber = 'INV-' . $datePrefix . '-' . $timestamp . $microseconds . '-' . str_pad($attempts, 3, '0', STR_PAD_LEFT);

                if (!static::withoutGlobalScope('user')->where('invoice_number', $invoiceNumber)->exists()) {
                    return $invoiceNumber;
                }
            }

            $attempts++;
            usleep(rand(1000, 5000));
        }

        // Final fallback - use timestamp with random
        $microtime = explode(' ', microtime());
        $timestamp = $microtime[1];
        $microseconds = str_pad(substr($microtime[0], 2, 6), 6, '0');
        $random = mt_rand(1000, 9999);
        return 'INV-' . $datePrefix . '-' . $timestamp . $microseconds . '-' . $random;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->invoice_number)) {
                $sale->invoice_number = static::generateInvoiceNumber();
            }
        });
    }



    /**
     * Get the sales details for this sale
     */
    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class);
    }

    /**
     * Get the total profit for this sale
     */
    public function getTotalProfitAttribute()
    {
        return $this->salesDetails->sum(function ($detail) {
            // Handle null product (deleted products)
            if (!$detail->product) {
                // If product is deleted, calculate profit based on unit_price only
                // (assuming cost is 0 for deleted products)
                return $detail->unit_price * $detail->quantity;
            }
            return ($detail->unit_price - ($detail->product->cost ?? 0)) * $detail->quantity;
        });
    }

    /**
     * Get the total items count
     */
    public function getTotalItemsAttribute()
    {
        return $this->salesDetails->sum('quantity');
    }

    /**
     * Scope for completed sales
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for sales by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    /**
     * Scope for sales by payment method
     */
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope for today's sales
     */
    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    /**
     * Scope for this week's sales
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's sales
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('sale_date', now()->month)
                    ->whereYear('sale_date', now()->year);
    }

    /**
     * Get the total cost of goods sold for this sale
     */
    public function getCostOfGoodsSoldAttribute()
    {
        return $this->salesDetails->sum(function ($detail) {
            // Handle null product (deleted products)
            if (!$detail->product) {
                return 0; // Cost is 0 for deleted products
            }
            return ($detail->product->cost ?? 0) * $detail->quantity;
        });
    }

    /**
     * Get the profit margin percentage
     */
    public function getProfitMarginAttribute()
    {
        if ($this->total_amount == 0) return 0;
        
        $profit = $this->total_profit;
        return ($profit / $this->total_amount) * 100;
    }

    /**
     * Get the profit margin status color class
     */
    public function getProfitMarginColorAttribute()
    {
        $margin = $this->profit_margin;
        
        if ($margin >= 30) return 'text-green-600';
        if ($margin >= 15) return 'text-blue-600';
        if ($margin > 0) return 'text-yellow-600';
        return 'text-red-600';
    }


}

