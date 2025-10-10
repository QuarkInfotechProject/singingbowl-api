<?php

namespace Modules\Coupon\DTO;

use Modules\Shared\DTO\Constructor;

class CouponUpdateDTO extends Constructor
{
    public string $id;
    public string $name;
    public string $code;
    public string $type;
    public int|null $value;
    public int|null $maxDiscount;
    public string|null $startDate;
    public string|null $endDate;
    public bool $isActive = false;
    public bool $isPublic = false;
    public bool $isBulkOffer = false;
    public int|null $minimumSpend;

    /**
     * @var CouponProductDTO[]
     */
    public $products;

    /**
     * @var CouponExcludeProductDTO[]
     */
    public $excludeProducts;

    public int|null $usageLimitPerCoupon;
    public int|null $usageLimitPerCustomer;
    public int|null $minQuantity;

    public bool $applyAutomatically;
    public bool $individualUse;
    public $paymentMethods;

    /**
     * @var array|null
     */
    public $relatedCoupons;

    /**
     * @var array|null
     */
    public $excludedCoupons;
}
