<?php

namespace Modules\Others\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\App\Models\Product;

class DarazAnalytics extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_id',
        'units_sold',
        'reviews_count',
        'link',
        'is_active'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
