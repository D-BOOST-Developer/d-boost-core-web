<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitLoss extends Model
{
    use HasFactory, HasUuidAndAudit;

    protected $fillable = [
        'period_start',
        'period_end',
        'period_type',
        'total_revenue',
        'total_expenses',
        'gross_profit',
        'net_profit',
        'profit_margin',
        'revenue_breakdown',
        'expense_breakdown',
        'is_calculated',
        'calculated_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'total_revenue' => 'decimal:2',
            'total_expenses' => 'decimal:2',
            'gross_profit' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'profit_margin' => 'decimal:4',
            'revenue_breakdown' => 'array',
            'expense_breakdown' => 'array',
            'is_calculated' => 'boolean',
            'calculated_at' => 'datetime',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($profitLoss) {
            // Auto-calculate profit values handling nulls safely
            $revenue = (float)($profitLoss->total_revenue ?? 0);
            $expenses = (float)($profitLoss->total_expenses ?? 0);
            
            $profitLoss->gross_profit = $revenue - $expenses;
            $profitLoss->net_profit = $profitLoss->gross_profit; // Can be adjusted for taxes, etc.
            
            if ($revenue > 0) {
                $profitLoss->profit_margin = ($profitLoss->net_profit / $revenue) * 100;
            } else {
                $profitLoss->profit_margin = 0;
            }
            
            $profitLoss->calculated_at = now();
            $profitLoss->is_calculated = true;
        });
    }

    /**
     * Calculate profit/loss for a specific period
     */
    public static function calculateForPeriod($startDate, $endDate, $periodType = 'custom')
    {
        // Calculate total revenue from sales
        $totalRevenue = Sale::completed()
            ->dateRange($startDate, $endDate)
            ->sum('total_amount');

        // Calculate total expenses
        $totalExpenses = Expense::dateRange($startDate, $endDate)
            ->sum('amount');

        // Get revenue breakdown by payment method
        $revenueBreakdown = Sale::completed()
            ->dateRange($startDate, $endDate)
            ->selectRaw('payment_method, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        // Get expense breakdown by category
        $expenseBreakdown = Expense::dateRange($startDate, $endDate)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        return self::updateOrCreate(
            [
                'period_start' => $startDate,
                'period_end' => $endDate,
                'period_type' => $periodType,
            ],
            [
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'revenue_breakdown' => $revenueBreakdown,
                'expense_breakdown' => $expenseBreakdown,
            ]
        );
    }

    /**
     * Scope for specific period type
     */
    public function scopeByPeriodType($query, $type)
    {
        return $query->where('period_type', $type);
    }

    /**
     * Scope for profitable periods
     */
    public function scopeProfitable($query)
    {
        return $query->where('net_profit', '>', 0);
    }

    /**
     * Scope for loss periods
     */
    public function scopeLoss($query)
    {
        return $query->where('net_profit', '<', 0);
    }

    /**
     * Get formatted period
     */
    public function getPeriodAttribute()
    {
        return $this->period_start->format('M d, Y') . ' - ' . $this->period_end->format('M d, Y');
    }

    /**
     * Check if this period is profitable
     */
    public function isProfitable(): bool
    {
        return $this->net_profit > 0;
    }

    /**
     * Get profit status
     */
    public function getProfitStatusAttribute()
    {
        if ($this->net_profit > 0) return 'profit';
        if ($this->net_profit < 0) return 'loss';
        return 'break_even';
    }
}
