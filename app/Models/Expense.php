<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'amount',
        'expense_date',
        'category',
        'receipt_path',
        'payment_method',
        'is_recurring',
        'recurring_period',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
            'is_recurring' => 'boolean',
        ];
    }

    /**
     * Get expense categories
     */
    public static function getCategories()
    {
        return [
            'rent' => 'Rent',
            'utilities' => 'Utilities',
            'supplies' => 'Supplies',
            'marketing' => 'Marketing',
            'transportation' => 'Transportation',
            'maintenance' => 'Maintenance',
            'insurance' => 'Insurance',
            'salaries' => 'Salaries',
            'taxes' => 'Taxes',
            'other' => 'Other',
        ];
    }

    /**
     * Get payment methods
     */
    public static function getPaymentMethods()
    {
        return [
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'bank_transfer' => 'Bank Transfer',
            'e_wallet' => 'E-Wallet',
        ];
    }

    /**
     * Scope for expenses by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for expenses by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * Scope for recurring expenses
     */
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    /**
     * Scope for today's expenses
     */
    public function scopeToday($query)
    {
        return $query->whereDate('expense_date', today());
    }

    /**
     * Scope for this week's expenses
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's expenses
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', now()->month)
                    ->whereYear('expense_date', now()->year);
    }

    /**
     * Get formatted category name
     */
    public function getCategoryNameAttribute()
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Get the full URL for the expense receipt
     */
    public function getReceiptUrlAttribute()
    {
        if ($this->receipt_path) {
            return asset('storage/' . $this->receipt_path);
        }
        return null;
    }

    /**
     * Check if expense has a receipt
     */
    public function hasReceipt()
    {
        return !empty($this->receipt_path) && Storage::disk('public')->exists($this->receipt_path);
    }
}
