<?php

namespace Modules\Cart\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductVariant;
use Modules\Product\App\Models\ProductOptionValue;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'variant_options',
        'purchased_price',
        'quantity'
    ];

    protected $casts = [
        'variant_options' => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function getVariantOptionValues()
    {
        if ($this->variant_id) {
            return ProductOptionValue::whereHas('variants', function ($query) {
                $query->where('product_variant_id', $this->variant_id);
            })->with('option')->get();
        }

        return collect([]);
    }
}
