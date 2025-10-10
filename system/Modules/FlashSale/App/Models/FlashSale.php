<?php
namespace Modules\FlashSale\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Color\App\Models\Color;
use Modules\FlashSale\App\Models\FlashSaleProduct;
use Modules\Media\Trait\HasMedia;

class FlashSale extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'campaign_name',
        'theme_color',
        'text_color',
        'start_date',
        'end_date',
    ];

    public static function boot()
    {
        parent::boot();

         static::saved(function ($flashSale) {
            $flashSale->syncFiles(request('files', []));
        });

        static::deleting(function ($flashSale) {
            $flashSale->files()->detach();
        });
    }

    /**
     * Many-to-Many Relationship using UUID for products.
     */
    public function products()
    {
        return $this->belongsToMany(
            \Modules\Product\App\Models\Product::class,
            'flash_sale_products',
            'flash_sale_id',
            'product_id'
        )->withPivot(['original_price', 'special_price'])
        ->withTimestamps();
    }

    public function flashSaleProducts()
    {
        return $this->hasMany(FlashSaleProduct::class, 'flash_sale_id');
    }

    public function themeColor()
    {
        return $this->belongsTo(Color::class, 'theme_color');
    }

    public function textColor()
    {
        return $this->belongsTo(Color::class, 'text_color');
    }

    public function getDesktopBanner()
    {
        return $this->files()->firstWhere('type', 'desktopBanner');
    }

    public function getMobileBanner()
    {
        return $this->files()->firstWhere('type', 'mobileBanner');
    }

    public function scopeWithEverything($query)
    {
        return $query->with(['products', 'files', 'themeColor', 'textColor']);
    }
}
