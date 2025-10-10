<?php

namespace Modules\Product\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Attribute\App\Models\AttributeValue;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $with = ['attributeValue'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['product_attribute_id', 'attribute_value_id'];

    public $timestamps = false;

    public function exists()
    {
        return ! is_null($this->attributeValue);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }
}
