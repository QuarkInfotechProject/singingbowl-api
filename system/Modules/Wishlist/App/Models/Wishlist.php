<?php

namespace Modules\Wishlist\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\App\Models\Product;
use Modules\User\App\Models\User;

class Wishlist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'user_agent'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'wishlist_products');
    }
    public static function getWithAllRelations($wishlistId)
    {
        return self::with([
            'products',
            'products.reviews',
            'products.files',
            'products.categories',
            'products.variants',
            'products.variants.optionValues',
            'products.variants.optionValues.files',
            'products.variants.optionValues.option',
            'products.options',
            'products.brand'
        ])->find($wishlistId);
    }
}
