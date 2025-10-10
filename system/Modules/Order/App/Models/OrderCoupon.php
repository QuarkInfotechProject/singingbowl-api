<?php

namespace Modules\Order\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Coupon\App\Models\Coupon;

class OrderCoupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'coupon_id',
        'discount_amount'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
