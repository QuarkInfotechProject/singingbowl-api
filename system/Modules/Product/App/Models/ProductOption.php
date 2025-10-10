<?php

namespace Modules\Product\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'name',
        'has_image',
        'product_id'
    ];

    public function values()
    {
        return $this->hasMany(ProductOptionValue::class)->with('files');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
