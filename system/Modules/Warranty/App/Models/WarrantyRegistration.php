<?php

namespace Modules\Warranty\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WarrantyRegistration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'product_name',
        'quantity',
        'date_of_purchase',
        'purchased_from',
        'order_id',
        'address',
        'country_name',
        'province_name',
        'city_name',
        'zone_name'
    ];
}
