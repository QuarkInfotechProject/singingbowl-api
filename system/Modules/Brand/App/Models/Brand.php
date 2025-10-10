<?php

namespace Modules\Brand\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Media\Trait\HasMedia;
use Modules\Meta\Trait\HasMetaData;
use Modules\Product\App\Models\Product;

class Brand extends Model
{
    use HasFactory, HasMedia, HasMetaData;

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    /**
     * Boot method to prevent duplicate brand names.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (Brand::where('name', $brand->name)->exists()) {
                throw new \Exception('A brand with this name already exists.');
            }
        });

        static::saved(function ($entity) {
            $entity->saveMetaData(request('meta', []));
            $entity->syncFiles(request('files', []));
        });

        static::deleting(function ($entity) {
            $entity->files()->detach();

            if ($entity->meta) {
                $entity->meta->delete();
            }
        });
    }

    public function getLogo()
    {
        return $this->files()->firstWhere('type', 'logo');
    }

    public function getBanner()
    {
        return $this->files()->firstWhere('type', 'banner');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
