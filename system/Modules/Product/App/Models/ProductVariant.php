<?php

namespace Modules\Product\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Media\Trait\HasMedia;

class ProductVariant extends Model
{
    use HasFactory, HasMedia;

    protected $table = 'product_variants';
     /**
     * The attributes that are mass assignable.
     */
   protected $fillable = [
        'uuid',
        'name',
        'sku',
        'status',
        'original_price',
        'special_price',
        'special_price_start',
        'special_price_end',
        'quantity',
        'in_stock',
        'product_id',
    ];

    protected $casts = [
        'status'   => 'boolean',
        'in_stock' => 'boolean',
    ];

    /**
     * Set the quantity attribute and automatically update in_stock status.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $value;
        $this->attributes['in_stock'] = ($value > 0) ? 1 : 0;
    }

    /**
     * Boot method to ensure consistency when saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($variant) {
            // Ensure in_stock is consistent with quantity on every save
            if (isset($variant->attributes['quantity'])) {
                $variant->attributes['in_stock'] = ($variant->attributes['quantity'] > 0) ? 1 : 0;
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function optionValues()
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_option_variants',
            'product_variant_id',
            'product_option_value_id'
        )->withPivot('product_id');
    }

    public function markAsOutOfStock()
    {
        $this->withoutEvents(function () {
            $this->update(['in_stock' => false]);
        });
    }

    public function markAsInStock()
    {
        $this->withoutEvents(function () {
            $this->update(['in_stock' => true]);
        });
    }

    public function getCurrentPriceAttribute()
    {
        if ($this->special_price &&
            (is_null($this->special_price_start) || now()->gte($this->special_price_start)) &&
            (is_null($this->special_price_end) || now()->lte($this->special_price_end))) {
            return $this->special_price;
        }
        return $this->original_price;
    }
}
