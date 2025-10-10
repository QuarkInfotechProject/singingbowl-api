<?php

namespace Modules\Cart\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Coupon\App\Models\Coupon;

class GuestCartCoupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['guest_cart_id', 'coupon_id', 'discount_amount'];

    public function guestCart()
    {
        return $this->belongsTo(GuestCart::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
