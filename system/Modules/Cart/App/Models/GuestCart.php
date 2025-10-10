<?php

namespace Modules\Cart\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Coupon\App\Models\Coupon;

class GuestCart extends Model
{
    use HasFactory;

    protected $table = 'guest_carts';

    protected $fillable = [
        'guest_token',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->guest_token)) {
                $model->guest_token = Str::uuid()->toString();
            }
        });
    }

    /**
     * Get the items for the guest cart.
     */
    public function items()
    {
        return $this->hasMany(GuestCartItem::class);
    }

    /**
     * Get the guest cart coupons.
     */
    public function guestCartCoupons()
    {
        return $this->hasMany(GuestCartCoupon::class);
    }

    /**
     * Get the coupons for the guest cart.
     */
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'guest_cart_coupons')
            ->withPivot('discount_amount')
            ->withTimestamps();
    }

    /**
     * Check if the guest cart has any applied coupons.
     */
    public function hasAppliedCoupons()
    {
        return $this->guestCartCoupons()->exists();
    }

    /**
     * Get all applied coupons for the guest cart.
     */
    public function getAppliedCoupons()
    {
        return $this->coupons;
    }

    /**
     * Apply a coupon to the guest cart.
     */
    public function applyCoupon(Coupon $coupon)
    {
        // Use database transactions to prevent race conditions
        return DB::transaction(function() use ($coupon) {
            // Check if cart has items
            if ($this->items->isEmpty()) {
                // Return zero discount for empty carts
                return [
                    'discountAmount' => 0,
                    'result' => [
                        'subTotal' => 0,
                        'totalDiscount' => 0,
                        'total' => 0
                    ]
                ];
            }

            if ($coupon->is_bulk_offer) {
                $bulkOfferCouponIds = $this->coupons()
                    ->where('is_bulk_offer', true)
                    ->pluck('coupons.id')
                    ->toArray();

                if (!empty($bulkOfferCouponIds)) {
                    $this->coupons()->detach($bulkOfferCouponIds);
                }
            }

            $discountAmount = $this->calculateDiscountAmount($coupon);

            $this->coupons()->attach($coupon->id, ['discount_amount' => $discountAmount]);

            $result = $this->recalculateTotal();

            return [
                'discountAmount' => $discountAmount,
                'result' => $result
            ];
        });
    }

    /**
     * Calculate the discount amount for a coupon.
     */
    public function calculateDiscountAmount(Coupon $coupon)
    {
        $discountAmount = 0;
        $subtotal = $this->subTotal();

        // Check if cart is empty or has no items
        if ($subtotal <= 0 || $this->items->isEmpty()) {
            return 0;
        }

        if ($coupon->type === Coupon::TYPE_PERCENTAGE) {
            $discountAmount = $subtotal * $coupon->value / 100;
            // Apply max_discount cap if set
            if ($coupon->max_discount && $discountAmount > $coupon->max_discount) {
                $discountAmount = $coupon->max_discount;
            }
        } else if ($coupon->type === Coupon::TYPE_FIXED_CART) {
            // Ensure discount doesn't exceed subtotal
            $discountAmount = min($coupon->value, $subtotal);
        } else if ($coupon->type === Coupon::TYPE_FREE_SHIPPING) {
            // Free shipping coupons typically don't provide a direct cart discount
            $discountAmount = 0;
        }

        return $discountAmount;
    }

    /**
     * Recalculate the cart total.
     */
    public function recalculateTotal()
    {
        $subTotal = $this->subTotal();
        $totalDiscount = $this->totalDiscount();

        // Ensure discount doesn't exceed subtotal (prevent negative totals)
        $totalDiscount = min($totalDiscount, $subTotal);
        $total = $subTotal - $totalDiscount;

        return [
            'subTotal' => $subTotal,
            'totalDiscount' => $totalDiscount,
            'total' => $total
        ];
    }

    /**
     * Calculate the total discount for the cart.
     */
    public function totalDiscount()
    {
        return $this->guestCartCoupons()->sum('discount_amount');
    }

    /**
     * Calculate the subtotal for the cart.
     */
    public function subTotal()
    {
        return $this->items->sum(function ($item) {
            $priceSource = $item->variant ?? $item->product;
            $unitPrice = $priceSource->original_price;

            if ($priceSource->special_price &&
                (is_null($priceSource->special_price_start) || now()->gte($priceSource->special_price_start)) &&
                (is_null($priceSource->special_price_end) || now()->lte($priceSource->special_price_end))) {
                $unitPrice = $priceSource->special_price;
            }

            return $item->quantity * $unitPrice;
        });
    }

    /**
     * Count the total number of items in the cart.
     */
    public function totalItems()
    {
        return $this->items
            ->filter(function ($item) {
                foreach ($item->product->categories as $category) {
                    if ($category->name === 'Ncell') {
                        return false;
                    }
                }
                return true;
            })
            ->sum('quantity');
    }

    /**
     * Remove all coupons from the cart.
     */
    public function removeAllCoupons()
    {
        return $this->coupons()->detach();
    }

    /**
     * Static method to get a guest cart with all relations loaded.
     */
    public static function getWithAllRelations($cartId)
    {
        return self::with([
            'items',
            'items.product',
            'items.product.categories',
            'items.product.files',
            'items.variant',
            'items.variant.optionValues',
            'items.variant.optionValues.option',
            'items.variant.optionValues.files',
            'coupons'
        ])->find($cartId);
    }

    /**
     * Check if the cart has any of the specified products.
     * Implementation mirrors the Cart model method for compatibility.
     */
    public function hasAnyProduct($productIds)
    {
        $productIds = collect($productIds)->pluck('id')->toArray();

        return $this->items()
            ->whereIn('product_id', $productIds)
            ->get()
            ->pluck('product');
    }
}
