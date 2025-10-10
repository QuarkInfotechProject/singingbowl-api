<?php

namespace Modules\Coupon\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Cart\App\Models\Cart;
use Modules\Order\App\Models\Order;
use Modules\Product\App\Models\Product;

class Coupon extends Model
{
    use HasFactory;

    // Updated Coupon type constants
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED_CART = 'fixed_cart';
    public const TYPE_FREE_SHIPPING = 'free_shipping';

    /**
     * Get an associative array of coupon types.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_PERCENTAGE => 'Percentage Discount',
            self::TYPE_FIXED_CART => 'Fixed Cart Discount',
            self::TYPE_FREE_SHIPPING => 'Free Shipping',
        ];
    }

    /**
     * The attributes that are mass assignable.
     * 'is_percent' and 'free_shipping' are removed as they are no longer DB columns.
     */
    protected $fillable = [
        'name',
        'code',
        'type', // Primary way to determine coupon behavior
        'value',
        'max_discount',
        // 'free_shipping', // Removed
        // 'is_percent', // Removed
        'start_date',
        'end_date',
        'is_active',
        'is_public',
        'is_displayed',
        'is_bulk_offer',
        'minimum_spend',
        'usage_limit_per_coupon',
        'usage_limit_per_customer',
        'min_quantity',
        'apply_automatically',
        'individual_use_only',
        'payment_methods'
    ];

    /**
     * The attributes that should be cast.
     * 'is_percent' and 'free_shipping' are removed.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean', // Added to casts if it's a boolean column
        'is_bulk_offer' => 'boolean',
        'apply_automatically' => 'boolean',
        'individual_use_only' => 'boolean',
        'payment_methods' => 'array',
    ];

    protected $dates = ['start_date', 'end_date'];

    // Helper methods based on type
    public function isPercentageType(): bool
    {
        return $this->type === self::TYPE_PERCENTAGE;
    }

    public function isFixedCartType(): bool
    {
        return $this->type === self::TYPE_FIXED_CART;
    }

    public function isFreeShippingType(): bool
    {
        return $this->type === self::TYPE_FREE_SHIPPING;
    }

    public static function boot()
    {
        parent::boot();

        static::saved(function ($entity) {
            if (request() && method_exists($entity, 'saveRelations')) { // Check if request is available
                $entity->saveRelations(request()->all());
            }
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_products')
            ->withPivot('exclude')
            ->wherePivot('exclude', false);
    }

    public function exclude()
    {
        return $this->belongsToMany(Product::class, 'coupon_products')
            ->withPivot('exclude')
            ->wherePivot('exclude', true);
    }

    public function combinableCoupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_relations', 'coupon_id', 'related_coupon_id')
            ->wherePivot('type', 'include');
    }

    public function excludedCoupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_relations', 'coupon_id', 'related_coupon_id')
            ->wherePivot('type', 'exclude');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_coupons')
            ->withPivot('discount_amount')
            ->withTimestamps();
    }

    public function saveRelations(array $attributes)
    {
        $this->syncProducts(Arr::get($attributes, 'products', []));
        $this->syncExcludeProducts(Arr::get($attributes, 'excludeProducts', []));
        $this->syncRelatedCoupons(Arr::get($attributes, 'relatedCoupons', []));
        $this->syncExcludedCoupons(Arr::get($attributes, 'excludedCoupons', []));
    }

    protected function syncRelatedCoupons($coupons)
    {
        if (!is_array($coupons)) $coupons = [];
        $couponIds = Coupon::whereIn('id', $coupons)->pluck('id')->toArray();

        $this->combinableCoupons()->sync(
            $this->makeSyncList($couponIds, ['type' => 'include'])
        );
    }

    protected function syncExcludedCoupons($excludedCoupons)
    {
        if (!is_array($excludedCoupons)) $excludedCoupons = [];
        $excludedCouponIds = Coupon::whereIn('id', $excludedCoupons)->pluck('id')->toArray();

        $this->excludedCoupons()->sync(
            $this->makeSyncList($excludedCouponIds, ['type' => 'exclude'])
        );
    }

    protected function syncProducts($products)
    {
        if (!is_array($products)) $products = [];
        $productIds = Product::whereIn('uuid', $products)->pluck('id')->toArray();

        $this->products()->sync(
            $this->makeSyncList($productIds, ['exclude' => false])
        );
    }

    protected function syncExcludeProducts($excludeProducts)
    {
        if (!is_array($excludeProducts)) $excludeProducts = [];
        $excludeProductIds = Product::whereIn('uuid', $excludeProducts)->pluck('id')->toArray();

        $this->exclude()->sync(
            $this->makeSyncList($excludeProductIds, ['exclude' => true])
        );
    }

    private function makeSyncList($items, $pivotData)
    {
        if (empty($items)) {
            return [];
        }
        $pivotDataArray = array_fill(0, count($items), $pivotData);
        return array_combine($items, $pivotDataArray);
    }

    public function getIncludedAndExcludedProducts()
    {
        $includedProducts = $this->products->pluck('uuid');
        $excludedProducts = $this->exclude->pluck('uuid');

        return [
            'products' => $includedProducts,
            'excludeProducts' => $excludedProducts,
        ];
    }

    public function getIncludedAndExcludedCoupons()
    {
        $includedCoupons = $this->combinableCoupons->pluck('id');
        $excludedCoupons = $this->excludedCoupons->pluck('id');

        return [
            'relatedCoupons' => $includedCoupons,
            'excludedCoupons' => $excludedCoupons,
        ];
    }

    /**
     * Find a coupon by its code.
     */
    public static function findByCode($code)
    {
        return self::where(DB::raw('BINARY `code`'), $code)
            ->select(
                'id',
                'name',
                'code',
                'type',
                'value',
                'max_discount',
                'is_bulk_offer',
                'individual_use_only',
                'minimum_spend',
                'start_date',
                'end_date',
                'usage_limit_per_coupon',
                'usage_limit_per_customer',
                'used',
                'min_quantity',
                'payment_methods'
            )
            ->where('is_active', true)
            ->first();
    }

    public function didNotSpendTheRequiredAmount($cart = null)
    {
        if (is_null($this->minimum_spend) || $this->minimum_spend == 0) {
            return false;
        }

        // Use provided cart or get current user's cart
        if (!$cart) {
            $cart = Cart::getForCurrentUser();
        }

        return !$cart || $cart->subTotal() < $this->minimum_spend;
    }
    private function hasStartDate()
    {
        return ! is_null($this->start_date);
    }

    private function hasEndDate()
    {
        return ! is_null($this->end_date);
    }
    private function startDateIsValid()
    {
        return today() >= $this->start_date;
    }

    private function endDateIsValid()
    {
        return today() <= $this->end_date;
    }

    public function valid()
    {
        if ($this->hasStartDate() && $this->hasEndDate()) {
            return $this->startDateIsValid() && $this->endDateIsValid();
        }

        if ($this->hasStartDate()) {
            return $this->startDateIsValid();
        }

        if ($this->hasEndDate()) {
            return $this->endDateIsValid();
        }

        return true;
    }

    public function invalid()
    {
        $isInvalid = !$this->valid();

        if ($isInvalid) {
            $this->update(['is_active' => false]);
        }

        return $isInvalid;
    }

    public function usageLimitReached($userId = null)
    {
        return $this->perCouponUsageLimitReached();
    }

    public function perCouponUsageLimitReached()
    {
        if (is_null($this->usage_limit_per_coupon)) {
            return false;
        }
        return $this->used >= $this->usage_limit_per_coupon;
    }
}
