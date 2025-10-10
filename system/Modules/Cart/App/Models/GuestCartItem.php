<?php

namespace Modules\Cart\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductOptionValue;
use Modules\Product\App\Models\ProductVariant;

class GuestCartItem extends Model
{
    use HasFactory;

    protected $table = 'guest_cart_items';

    protected $fillable = [
        'guest_cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'purchased_price',
        'variant_options',
    ];

    protected $casts = [
        'variant_options' => 'array',
    ];

    /**
     * Get the guest cart that owns the item.
     */
    public function guestCart()
    {
        return $this->belongsTo(GuestCart::class, 'guest_cart_id');
    }

    /**
     * Get the product associated with the cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variant associated with the cart item (if any).
     */
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

    /**
     * Define the factory for the model.
     */
    // protected static function newFactory()
    // {
    //     return \Modules\Cart\Database\factories\GuestCartItemFactory::new();
    // }
}
