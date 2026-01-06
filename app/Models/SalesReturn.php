<?php

namespace App\Models;

use App\Traits\HasUuidAndAudit;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturn extends Model
{
    use HasFactory, HasUuidAndAudit, BelongsToUser, SoftDeletes;

    protected $fillable = [
        'user_id',
        'return_number',
        'sale_id',
        'return_date',
        'reason', // 'defective', 'wrong_item', 'customer_request', 'other'
        'total_return_amount',
        'refund_amount',
        'status', // 'pending', 'completed', 'cancelled'
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'return_date' => 'date',
            'total_return_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($return) {
            if (empty($return->return_number)) {
                $return->return_number = static::generateReturnNumber();
            }
            
            // Auto-set user_id from sale if not set
            if (empty($return->user_id) && $return->sale) {
                $return->user_id = $return->sale->user_id;
            }
        });
    }

    /**
     * Generate unique return number
     */
    public static function generateReturnNumber()
    {
        $datePrefix = date('Ymd');
        $sequentialNumber = static::whereDate('created_at', today())->count() + 1;
        return 'RET-' . $datePrefix . '-' . str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the original sale
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the return items
     */
    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }

    /**
     * Scope for pending returns
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for cancelled returns
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for completed returns
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('return_date', [$startDate, $endDate]);
    }

    /**
     * Get reason label
     */
    public function getReasonLabel()
    {
        $reasons = [
            'defective' => 'Defective Product',
            'wrong_item' => 'Wrong Item',
            'customer_request' => 'Customer Request',
            'other' => 'Other',
        ];
        return $reasons[$this->reason] ?? ucfirst($this->reason);
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        $labels = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Complete the return and restock inventory
     * Simplified workflow: instantly complete and restock
     */
    public function complete()
    {
        // Create stock movements for all items
        foreach ($this->items as $item) {
            StockMovement::create([
                'product_id' => $item->product_id,
                'movement_type' => 'in',
                'quantity' => $item->quantity,
                'reference_type' => 'return',
                'reference_id' => $this->id,
                'notes' => 'Sales Return ' . $this->return_number,
                'movement_date' => now(),
                'created_by' => auth()->id(),
            ]);

            // Update product stock
            $item->product->increaseStock($item->quantity);
        }
        
        // Mark as completed
        $this->update(['status' => 'completed']);
    }
    
    /**
     * Cancel the return
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason ? "Cancelled: {$reason}" : $this->notes,
        ]);
    }
}
