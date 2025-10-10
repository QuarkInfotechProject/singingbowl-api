<?php

namespace Modules\Product\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Attribute\App\Models\Attribute;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $with = ['attribute', 'values'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['product_id', 'attribute_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_attribute_id')->with('attributeValue:id,value');
    }
}
