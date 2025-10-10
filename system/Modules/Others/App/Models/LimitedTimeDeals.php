<?php

namespace Modules\Others\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\App\Models\Product;

class LimitedTimeDeals extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_id',
        'status',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the product associated with the limited time deal.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to only include active deals.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to only include inactive deals.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Find a product by its UUID and get its ID
     *
     * @param string $uuid
     * @return int|null
     */
    public static function getProductIdByUuid($uuid)
    {
        $product = Product::where('uuid', $uuid)->first();
        return $product ? $product->id : null;
    }
}