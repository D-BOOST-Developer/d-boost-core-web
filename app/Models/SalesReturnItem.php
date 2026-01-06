<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SalesReturnItem extends Model
{
    use HasFactory, BelongsToUser, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = Str::uuid();
            }
            
            // Auto-set user_id from sales_return if not set
            if (empty($model->user_id) && $model->salesReturn) {
                $model->user_id = $model->salesReturn->user_id;
            }
        });
    }

    protected $fillable = [
        'user_id',
        'sales_return_id',
        'product_id',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    /**
     * Get the sales return
     */
    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate line total
     */
    public function calculateLineTotal()
    {
        return $this->line_total = $this->quantity * $this->unit_price;
    }
}
