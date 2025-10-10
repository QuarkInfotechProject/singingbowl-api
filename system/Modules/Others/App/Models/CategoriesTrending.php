<?php
namespace Modules\Others\App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Media\Trait\HasMedia;
use Modules\Product\App\Models\Product;
use Modules\Category\App\Models\Category;
class CategoriesTrending extends Model
{
    use HasFactory, HasMedia;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'category_id',
        'sort_order',
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

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function products()
    {
        return $this->belongsTo(Category::class, 'category_id')
            ->with('products');
    }
}