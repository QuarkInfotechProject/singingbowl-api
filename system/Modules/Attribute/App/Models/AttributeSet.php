<?php

namespace Modules\Attribute\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttributeSet extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attributeSet) {
            $usedAttributes = $attributeSet->attributes()
                ->whereHas('productAttribute.product')
                ->with('productAttribute.product')
                ->get();

            if ($usedAttributes->isNotEmpty()) {
                $details = $usedAttributes->map(function ($attribute) {
                    $productNames = $attribute->productAttribute->map(function ($productAttribute) {
                        return $productAttribute->product->product_name ?? 'Unknown Product';
                    })->unique();

                    return [
                        'attribute' => $attribute->name ?? 'Unnamed Attribute',
                        'products' => $productNames->join(', ')
                    ];
                });

                $message = $details->map(function ($detail) {
                    return "Attribute: {$detail['attribute']} is used by Products: {$detail['products']}";
                })->join('; ');

                throw new \Exception("This attribute set cannot be deleted because: $message.");
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name'
    ];

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }
}
