<?php

namespace Modules\Content\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Media\App\Models\File;
use Modules\Media\Trait\HasMedia;

class BestSeller extends Model
{
    use HasFactory, HasMedia;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'link',
        'is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

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
