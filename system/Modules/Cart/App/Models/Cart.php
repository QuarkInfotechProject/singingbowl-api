<?php

namespace Modules\Cart\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Coupon\App\Models\Coupon;
use Modules\User\App\Models\User;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'user_agent'
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartCoupons()
    {
        return $this->hasMany(CartCoupon::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'cart_coupons')
            ->withPivot('discount_amount')
            ->withTimestamps();
    }

    public function hasAppliedCoupons()
    {
        return $this->cartCoupons()->exists();
    }

    public function getAppliedCoupons()
    {
        return $this->coupons;
    }

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

    public function totalDiscount()
    {
        return $this->cartCoupons()->sum('discount_amount');
    }

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
     * Get cart for the current authenticated user.
     * This method now only serves authenticated users.
     * Guest carts are handled by GuestCartService and GuestCart model.
     */
    public static function getForCurrentUser(): ?self
    {
        $user = Auth::user();

        if ($user) {
            try {
                return self::where('user_id', $user->id)->first();
            } catch (\Exception $e) {
                // Error retrieving cart
            }
        }

        return null;
    }

    /**
     * Get a cart with all its related data efficiently loaded.
     * This method helps avoid N+1 query issues by eager loading relationships.
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

    public function hasAnyProduct($productIds)
    {
        $productIds = collect($productIds)->pluck('id')->toArray();

        return $this->items()
            ->whereIn('product_id', $productIds)
            ->get()
            ->pluck('product');
    }

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

    public function removeAllCoupons()
    {
        return DB::transaction(function() {
            $this->coupons()->detach();
            return true;
        });
    }
}
