<?php

namespace Modules\Others\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\App\Models\Product;

class Features extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'text',
        'is_active',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_features', 'feature_id', 'product_id');
    }
}
