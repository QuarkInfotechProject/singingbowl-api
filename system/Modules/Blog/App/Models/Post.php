<?php

namespace Modules\Blog\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Media\Trait\HasMedia;
use Modules\Meta\Trait\HasMetaData;

class Post extends Model
{
    use HasFactory, HasMedia, HasMetaData;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'slug',
        'read_time',
        'description',
        'is_active'
    ];

    public static function boot()
    {
        parent::boot();

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
}
