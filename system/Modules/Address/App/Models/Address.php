<?php

namespace Modules\Address\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\App\Models\OrderAddress;
use Modules\User\App\Models\User;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'first_name',
        'last_name',
        'mobile',
        'backup_mobile',
        'address',
        'country_name',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'zone_id',
        'zone_name'
    ];

    public function orderAddress()
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
