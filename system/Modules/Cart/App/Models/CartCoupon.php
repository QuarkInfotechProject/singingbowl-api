<?php

namespace Modules\Cart\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Coupon\App\Models\Coupon;

class CartCoupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['cart_id', 'coupon_id', 'discount_amount'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
