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
        
        // New Fields
        'email',
        'address_line_1', // Renamed from 'address'
        'address_line_2',
        'postal_code',
        'landmark',
        'address_type',
        'delivery_instructions',
        'is_default',
        'label',
	'country_code',

        // Existing Fields
        'first_name',
        'last_name',
        'mobile',
        'backup_mobile',
        'country_name',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'zone_id',
        'zone_name'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_default' => 'boolean',
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
