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

    /**
     * The model uses a composite key and does not have an auto-incrementing id.
     *
     * @var bool
     */
    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'product_attribute_id';

    public function exists()
    {
        return ! is_null($this->attributeValue);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('product_attribute_id', $this->getAttribute('product_attribute_id'))
            ->where('attribute_value_id', $this->getAttribute('attribute_value_id'));
    }
}
