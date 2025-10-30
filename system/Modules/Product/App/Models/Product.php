<?php

namespace Modules\Product\App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
// use Laravel\Scout\Searchable; // Temporarily disabled to avoid Meilisearch dependency during product creation
use Modules\Category\App\Models\Category;
use Modules\Media\Trait\HasMedia;
use Modules\Meta\Trait\HasMetaData;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderItem;
use Modules\Others\App\Models\ActiveOffer;
use Modules\Others\App\Models\DarazAnalytics;
use Modules\Others\App\Models\Features;
use Modules\Review\App\Models\Review;
use Modules\Tag\App\Models\Tag;
use Modules\Wishlist\App\Models\Wishlist;
use Modules\Brand\App\Models\Brand;
use \Modules\FlashSale\App\Models\FlashSale;

class Product extends Model
{
    use HasFactory, HasMedia, HasMetaData; // Searchable trait disabled while Meilisearch is offline
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'product_name',
        'slug',
        'brand_id',
        'sort_order',
        'best_seller',
        'has_variant',
        'original_price',
        'special_price',
        'special_price_start',
        'special_price_end',
        'sku',
        'description',
        'additional_description',
        'status',
        'sale_start',
        'sale_end',
        'quantity',
        'in_stock',
        'new_from',
        'new_to',
        'specifications',
        'key_specs'
    ];

    protected $casts = [
        'status' => 'boolean',
        'in_stock' => 'boolean',
        'has_variant' => 'boolean',
        'specifications' => 'array',
        'key_specs' => 'array'
    ];

    protected $dates = ['special_price_start', 'special_price_end', 'sale_start', 'sale_end', 'new_from', 'new_to'];

    /**
     * Set the quantity attribute and automatically update in_stock status.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $value;
        $this->attributes['in_stock'] = ($value > 0) ? 1 : 0;
    }

    // Meilisearch indexing disabled while search infrastructure is unavailable.
    /*
    public function searchableAs(): string
    {
        return 'products';
    }

    public function toSearchableArray(): array
    {
        $array = [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'original_price' => (float) $this->original_price,
            'special_price' => (float) $this->special_price,
            'status' => $this->status,
            'in_stock' => $this->in_stock,
            'key_specs' => $this->key_specs,
        ];

        if ($this->brand) {
            $array['brand_name'] = $this->brand->name;
            $array['brand_id'] = $this->brand->id;
        } else {
            $this->load('brand');
            if ($this->brand) {
                $array['brand_name'] = $this->brand->name;
                $array['brand_id'] = $this->brand->id;
            }
        }

        if ($this->categories && $this->categories->count() > 0) {
            $array['categories'] = $this->categories->pluck('name')->toArray();
            $array['category_ids'] = $this->categories->pluck('id')->toArray();
        } else {
            $this->load('categories');
            if ($this->categories && $this->categories->count() > 0) {
                $array['categories'] = $this->categories->pluck('name')->toArray();
                $array['category_ids'] = $this->categories->pluck('id')->toArray();
            }
        }

        return $array;
    }
    */

    public static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            if (isset($product->attributes['quantity'])) {
                $product->attributes['in_stock'] = ($product->attributes['quantity'] > 0) ? 1 : 0;
            }
        });

        static::saved(function ($entity) {
            $entity->saveMetaData(request('meta', []));
            $entity->syncFiles(request('files', []));
            $entity->saveRelations(request()->all());
        });
    }
    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }


    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class)->with('attribute:id,name');
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class,'product_id', 'id');
    }

    public function optionValues()
    {
        return $this->belongsToMany(ProductOptionValue::class, 'product_option_variants')
            ->withPivot('product_variant_id');
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(static::class, 'related_products', 'product_id', 'related_product_id');
    }

    public function upSellProducts()
    {
        return $this->belongsToMany(static::class, 'up_sell_products', 'product_id', 'up_sell_product_id');
    }

    public function crossSellProducts()
    {
        return $this->belongsToMany(static::class, 'cross_sell_products', 'product_id', 'cross_sell_product_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->belongsToMany(Wishlist::class, 'wishlist_products');
    }

    public function features()
    {
        return $this->belongsToMany(Features::class, 'product_features', 'product_id', 'feature_id');
    }

    public function activeOffers()
    {
        return $this->belongsToMany(ActiveOffer::class, 'product_active_offers', 'product_id', 'offer_id');
    }

    public function darazCount()
    {
        return $this->hasOne(DarazAnalytics::class);
    }

    public function getOnSaleAttribute()
    {
        return $this->onSale();
    }


    public function getIsNewAttribute()
    {
        return $this->isNew();
    }

    public function getTotalCompletedSoldAttribute()
    {
        return $this->hasMany(OrderItem::class)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', Order::DELIVERED)
            ->sum('order_items.quantity');
    }

    public function saveRelations($attributes = [])
    {
        if (array_key_exists('categories', $attributes)) {
            $this->categories()->sync(Arr::get($attributes, 'categories', []));
        }

        $this->tags()->sync(Arr::get($attributes, 'tags', []));

        $relatedProducts = Arr::get($attributes, 'relatedProducts', []);
        $relatedProductIds = Product::whereIn('uuid', $relatedProducts)->pluck('id')->toArray();
        $this->relatedProducts()->sync($relatedProductIds);

        $crossSells = Arr::get($attributes, 'crossSells', []);
        $crossSellProductIds = Product::whereIn('uuid', $crossSells)->pluck('id')->toArray();
        $this->crossSellProducts()->sync($crossSellProductIds);

        $upSells = Arr::get($attributes, 'upSells', []);
        $upSellProductIds = Product::whereIn('uuid', $upSells)->pluck('id')->toArray();
        $this->upSellProducts()->sync($upSellProductIds);
    }

    public function markAsOutOfStock()
    {
        $this->withoutEvents(function () {
            $this->update(['in_stock' => false]);
        });
    }
    public function keySpecs()
    {
        return $this->hasMany(ProductKeySpec::class);
    }


    public function markAsInStock()
    {
        $this->withoutEvents(function () {
            $this->update(['in_stock' => true]);
        });
    }

    public function isNew()
    {
        if ($this->hasNewFromDate() && $this->hasNewToDate()) {
            return $this->newFromDateIsValid() && $this->newToDateIsValid();
        }

        if ($this->hasNewFromDate()) {
            return $this->newFromDateIsValid();
        }

        if ($this->hasNewToDate()) {
            return $this->newToDateIsValid();
        }

        return false;
    }

    private function hasNewFromDate()
    {
        return !is_null($this->new_from);
    }

    private function hasNewToDate()
    {
        return !is_null($this->new_to);
    }

    private function newFromDateIsValid()
    {
        return today() >= $this->new_from;
    }

    private function newToDateIsValid()
    {
        return today() <= $this->new_to;
    }

    public function onSale()
    {
        return $this->hasValidSaleStartDate() && $this->hasValidSaleEndDate();
    }

    private function hasValidSaleStartDate()
    {
        return $this->sale_start && Carbon::now()->greaterThanOrEqualTo($this->sale_start);
    }

    private function hasValidSaleEndDate()
    {
        return !$this->sale_end || Carbon::now()->lessThanOrEqualTo($this->sale_end);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getOrderItemsSumQuantityAttribute()
    {
        return $this->orderItems()->sum('quantity');
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('has_variant', false)
                ->where('in_stock', true)
                ->orWhere(function (Builder $subQuery) {
                    $subQuery->where('has_variant', true)
                        ->whereHas('variants', function (Builder $variantQuery) {
                            $variantQuery->where('in_stock', true);
                        });
                });
        });
    }

    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('has_variant', false)
                ->where('in_stock', false)
                ->orWhere(function (Builder $subQuery) {
                    $subQuery->where('has_variant', true)
                        ->whereDoesntHave('variants', function (Builder $variantQuery) {
                            $variantQuery->where('in_stock', true);
                        });
                });
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function flashSales()
    {
        return $this->belongsToMany(
            FlashSale::class,
            'flash_sale_products',
            'product_id',
            'flash_sale_id'
        )->withPivot(['original_price', 'special_price'])
        ->withTimestamps();
    }

    public function getCurrentPriceAttribute()
    {
        if ($this->special_price &&
            (is_null($this->special_price_start) || now()->gte($this->special_price_start)) &&
            (is_null($this->special_price_end) || now()->lte($this->special_price_end))) {
            return $this->special_price;
        }
        return $this->original_price;
    }


}
