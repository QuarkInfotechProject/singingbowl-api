<?php

namespace Modules\Warranty\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WarrantyClaim extends Model
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
        'purchased_from',
        'images',
        'description',
        'address',
        'country_name',
        'province_name',
        'city_name',
        'zone_name'
    ];
}
