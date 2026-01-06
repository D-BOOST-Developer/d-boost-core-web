<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InventoryTransaction extends Model
{
    use SoftDeletes;

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     */
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'product_id',
        'transaction_type',
        'quantity',
        'stock_before',
        'stock_after',
        'unit_cost',
        'total_cost',
        'reference_type',
        'reference_id',
        'status',
        'reason',
        'notes',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'transaction_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'transaction_date' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot function for UUID and user tracking
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            
            // Auto-set user_id and created_by
            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
            if (empty($model->created_by)) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    /**
     * Global scope for multi-tenancy
     */
    protected static function booted()
    {
        static::addGlobalScope('user', function (Builder $query) {
            if (auth()->check()) {
                $query->where('inventory_transactions.user_id', auth()->id());
            }
        });
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withoutGlobalScope('user');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'auto_approved']);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRequiresApproval($query)
    {
        return $query->where('reference_type', 'manual')
                    ->where('status', 'pending');
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('transaction_date', [$from, $to]);
    }

    /**
     * Methods
     */
    public function approve($approvedBy = null)
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Only pending transactions can be approved');
        }

        return DB::transaction(function () use ($approvedBy) {
            // Update transaction status
            $this->update([
                'status' => 'approved',
                'approved_by' => $approvedBy ?? auth()->id(),
                'approved_at' => now(),
            ]);

            // Update product stock
            $product = $this->product;
            if ($this->isIncrease()) {
                $product->increment('stock_quantity', $this->quantity);
            } else {
                $product->decrement('stock_quantity', $this->quantity);
            }

            return $this;
        });
    }

    public function reject($reason, $rejectedBy = null)
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Only pending transactions can be rejected');
        }

        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => $rejectedBy ?? auth()->id(),
            'approved_at' => now(),
        ]);

        return $this;
    }

    public function isIncrease()
    {
        return in_array($this->transaction_type, [
            'purchase',
            'return_from_customer',
            'adjustment_increase',
            'transfer_in',
            'initial_stock'
        ]);
    }

    public function isDecrease()
    {
        return in_array($this->transaction_type, [
            'sale',
            'return_to_supplier',
            'adjustment_decrease',
            'transfer_out',
            'damaged',
            'expired'
        ]);
    }

    public function requiresApproval()
    {
        return $this->reference_type === 'manual' && 
               in_array($this->transaction_type, [
                   'adjustment_increase',
                   'adjustment_decrease',
                   'damaged',
                   'expired'
               ]);
    }

    /**
     * Accessors
     */
    public function getTransactionTypeNameAttribute()
    {
        return match($this->transaction_type) {
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'adjustment_increase' => 'Stock Increase',
            'adjustment_decrease' => 'Stock Decrease',
            'return_from_customer' => 'Return from Customer',
            'return_to_supplier' => 'Return to Supplier',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out',
            'damaged' => 'Damaged Items',
            'expired' => 'Expired Items',
            'initial_stock' => 'Initial Stock',
            default => ucwords(str_replace('_', ' ', $this->transaction_type)),
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'approved' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>',
            'auto_approved' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Auto Approved</span>',
            'rejected' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Unknown</span>',
        };
    }

    public function getReasonLabelAttribute()
    {
        return match($this->reason) {
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'damage' => 'Damage',
            'loss' => 'Loss/Missing',
            'theft' => 'Theft',
            'correction' => 'Stock Correction',
            'inventory_count' => 'Physical Inventory Count',
            'expired' => 'Expired',
            'return' => 'Return',
            'other' => 'Other',
            default => ucwords(str_replace('_', ' ', $this->reason ?? '')),
        };
    }
}

