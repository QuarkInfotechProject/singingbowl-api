<?php

namespace Modules\OrderProcessing\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderItem;

class Refund extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'amount',
        'reason',
        'restock_items'
    ];

    protected $casts = [
        'restock_items' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItems()
    {
        return $this->belongsToMany(OrderItem::class, 'order_item_refund')
            ->withPivot('quantity', 'amount')
            ->withTimestamps();
    }
}
