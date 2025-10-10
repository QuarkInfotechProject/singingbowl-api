<?php

namespace Modules\Attribute\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductAttribute;
use Modules\Category\App\Models\Category;

class Attribute extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attribute) {
                $usedProducts = $attribute->productAttribute()
                    ->whereHas('product', function ($query) {
                        $query->select('id', 'product_name');
                    })
                    ->with('product')
                    ->get();

            if ($usedProducts->isNotEmpty()) {
                $productNames = $usedProducts->map(function ($productAttribute) {
                    return $productAttribute->product->product_name ?? 'Unknown Product';
                })->unique();

                $productList = $productNames->join(', ');

                throw new \Exception("This attribute is being used by the following products: $productList. It cannot be deleted.");
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'attribute_set_id',
        'name',
        'is_enabled',
        'sort_order'

    ];
    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function productAttribute()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function attributeSet()
    {
        return $this->belongsTo(AttributeSet::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_attribute');
    }
}
