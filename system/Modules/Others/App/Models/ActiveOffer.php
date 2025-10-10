<?php

namespace Modules\Others\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Media\Trait\HasMedia;
use Modules\Product\App\Models\Product;

class ActiveOffer extends Model
{
    use HasFactory, HasMedia;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'text',
        'is_active'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($entity) {
            $entity->syncFiles(request('files', []));
        });

        static::deleting(function ($entity) {
            $entity->files()->detach();
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_active_offers', 'offer_id', 'product_id');
    }
}
