<?php

namespace Modules\Category\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Media\App\Models\File;
use Modules\Media\Trait\HasMedia;
use Modules\Product\App\Models\Product;
use Modules\Attribute\App\Models\Attribute;

class Category extends Model
{
    use HasFactory, HasMedia;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'slug',
        'is_searchable',
        'is_active',
        'is_displayed',
        'parent_id',
        'filter_price_min',
        'filter_price_max',
        'sort_order',
        'show_in_new_arrivals',
    ];

    public $timestamps = false;

    protected $casts = [
        'is_searchable' => 'boolean',
        'is_active' => 'boolean',
        'is_displayed' => 'boolean',
        'show_in_new_arrivals' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($entity) {
            $entity->syncFiles(request('files', []));
        });
    }
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'category_attribute')
            ->withPivot('sort_order', 'is_active')
            ->orderBy('category_attribute.sort_order');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    function getLogoAttribute()
    {
        return $this->files->where('pivot.zone', 'logo')->first() ?: new File;
    }

    function getBannerAttribute()
    {
        return $this->files->where('pivot.zone', 'banner')->first() ?: new File;
    }

    function toArray()
    {
        $attributes = parent::toArray();

        if ($this->relationLoaded('files')) {
            $attributes['files'] = [
                'logo' => [
                    'id' => $this->logo->id,
                    'url' => $this->logo->url,
                ],
                'banner' => [
                    'id' => $this->banner->id,
                    'url' => $this->banner->url,
                ],
            ];
        }

        return $attributes;
    }
}
