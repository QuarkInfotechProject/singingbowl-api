<?php

namespace Modules\Product\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductKeySpec extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_id',
        'spec_key',
        'spec_value',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => 'boolean',
        'spec_value' => 'array'
    ];

    /**
     * Get the product that owns the key spec.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}