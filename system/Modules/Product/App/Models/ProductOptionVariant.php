<?php

namespace Modules\Product\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOptionVariant extends Model
{
    use HasFactory;

    protected $table = 'product_option_variants';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
