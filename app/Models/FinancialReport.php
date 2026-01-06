<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialReport extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'report_type', // 'income_statement', 'balance_sheet', 'cash_flow', 'trial_balance'
        'period_start',
        'period_end',
        'total_revenue',
        'total_cost_of_goods_sold',
        'gross_profit',
        'total_operating_expenses',
        'operating_profit',
        'total_other_income',
        'total_other_expenses',
        'net_profit',
        'total_assets',
        'total_liabilities',
        'total_equity',
        'cash_beginning',
        'cash_from_operations',
        'cash_from_investing',
        'cash_from_financing',
        'cash_ending',
        'generated_by',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'total_revenue' => 'decimal:2',
            'total_cost_of_goods_sold' => 'decimal:2',
            'gross_profit' => 'decimal:2',
            'total_operating_expenses' => 'decimal:2',
            'operating_profit' => 'decimal:2',
            'total_other_income' => 'decimal:2',
            'total_other_expenses' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'total_assets' => 'decimal:2',
            'total_liabilities' => 'decimal:2',
            'total_equity' => 'decimal:2',
            'cash_beginning' => 'decimal:2',
            'cash_from_operations' => 'decimal:2',
            'cash_from_investing' => 'decimal:2',
            'cash_from_financing' => 'decimal:2',
            'cash_ending' => 'decimal:2',
        ];
    }

    /**
     * Get the generator user
     */
    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Scope for income statements
     */
    public function scopeIncomeStatement($query)
    {
        return $query->where('report_type', 'income_statement');
    }

    /**
     * Scope for balance sheets
     */
    public function scopeBalanceSheet($query)
    {
        return $query->where('report_type', 'balance_sheet');
    }

    /**
     * Scope for cash flow reports
     */
    public function scopeCashFlow($query)
    {
        return $query->where('report_type', 'cash_flow');
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('period_start', [$startDate, $endDate]);
    }

    /**
     * Get report type label
     */
    public function getReportTypeLabel()
    {
        $types = [
            'income_statement' => 'Income Statement',
            'balance_sheet' => 'Balance Sheet',
            'cash_flow' => 'Cash Flow Statement',
            'trial_balance' => 'Trial Balance',
        ];
        return $types[$this->report_type] ?? ucfirst($this->report_type);
    }

    /**
     * Get gross profit margin percentage
     */
    public function getGrossProfitMarginAttribute()
    {
        if ($this->total_revenue == 0) return 0;
        return ($this->gross_profit / $this->total_revenue) * 100;
    }

    /**
     * Get operating profit margin percentage
     */
    public function getOperatingProfitMarginAttribute()
    {
        if ($this->total_revenue == 0) return 0;
        return ($this->operating_profit / $this->total_revenue) * 100;
    }

    /**
     * Get net profit margin percentage
     */
    public function getNetProfitMarginAttribute()
    {
        if ($this->total_revenue == 0) return 0;
        return ($this->net_profit / $this->total_revenue) * 100;
    }

    /**
     * Get return on assets percentage
     */
    public function getReturnOnAssetsAttribute()
    {
        if ($this->total_assets == 0) return 0;
        return ($this->net_profit / $this->total_assets) * 100;
    }

    /**
     * Get return on equity percentage
     */
    public function getReturnOnEquityAttribute()
    {
        if ($this->total_equity == 0) return 0;
        return ($this->net_profit / $this->total_equity) * 100;
    }

    /**
     * Get debt to equity ratio
     */
    public function getDebtToEquityRatioAttribute()
    {
        if ($this->total_equity == 0) return 0;
        return $this->total_liabilities / $this->total_equity;
    }

    /**
     * Get current ratio
     */
    public function getCurrentRatioAttribute()
    {
        // Simplified - would need more detailed asset/liability breakdown
        if ($this->total_liabilities == 0) return 0;
        return $this->total_assets / $this->total_liabilities;
    }
}
