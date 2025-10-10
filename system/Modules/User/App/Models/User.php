<?php

namespace Modules\User\App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Modules\Address\App\Models\Address;
use Modules\Coupon\App\Models\Coupon;
use Modules\Order\App\Models\Order;
use Modules\Wishlist\App\Models\Wishlist;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    public const STATUS_ACTIVE = 1;
    public const STATUS_BLOCKED = 2;

    /**
     * @var string[]
     */
    public static $userStatus = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_BLOCKED => 'Blocked',
    ];

    protected $guard = 'user';


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'phone_no',
        'email',
        'date_of_birth',
        'gender',
        'offers_notification',
        'profile_picture',
        'full_name',
        'password',
        'status',
        'remarks',
        'oauth_type',
        'oauth_id',
        'last_login',
        'last_active_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
    ];

    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }

    public function timesUsedCoupon(Coupon $coupon)
    {
        return $this->orders()
            ->whereHas('coupons', function ($query) use ($coupon) {
                $query->where('coupons.id', $coupon->id);
            })
            ->count();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }
}
