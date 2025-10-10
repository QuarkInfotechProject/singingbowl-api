<?php

namespace Modules\Content\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Media\Trait\HasMedia;

class InThePress extends Model
{
    use HasFactory, HasMedia;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'link',
        'is_active',
        'published_date'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($entity) {
            $entity->syncFiles(request('files', []));
        });

        static::deleting(function ($content) {
            // Delete related files
            $content->files()->detach();
        });
    }
}
