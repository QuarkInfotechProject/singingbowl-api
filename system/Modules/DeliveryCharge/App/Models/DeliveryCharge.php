<?php

namespace Modules\DeliveryCharge\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryCharge extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'description',
        'delivery_charge',
        'additional_charge_per_item',
        'weight_based_charge'
    ];
}
