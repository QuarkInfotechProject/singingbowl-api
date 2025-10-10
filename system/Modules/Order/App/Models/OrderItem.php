<?php

namespace Modules\Order\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\OrderProcessing\App\Models\Refund;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductVariant;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'unit_price',
        'quantity',
        'line_total',
        'is_reviewed'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = ['is_reviewed' => 'boolean'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function refunds()
    {
        return $this->belongsToMany(Refund::class, 'order_item_refund')
            ->withPivot('quantity', 'amount')
            ->withTimestamps();
    }

    public function getTotalRefundedQuantityAttribute()
    {
        return $this->refunds()->sum('pivot.quantity');
    }

    public function getRemainingRefundableQuantityAttribute()
    {
        return $this->quantity - $this->total_refunded_quantity;
    }
}
