<?php
namespace Modules\FlashSale\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\App\Models\Product;
use Modules\FlashSale\App\Models\FlashSale;

class FlashSaleProduct extends Model
{
    protected $table = 'flash_sale_products';
    protected $fillable = ['flash_sale_id', 'product_id'];

    public function setProductIdAttribute($uuid)
    {
        $product = Product::where('uuid', $uuid)->firstOrFail();
        $this->attributes['product_id'] = $product->id;
    }

    public function getProductUuidAttribute()
    {
        return $this->product ? $this->product->uuid : null;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function flashSale()
    {
        return $this->belongsTo(FlashSale::class, 'flash_sale_id');
    }
}
