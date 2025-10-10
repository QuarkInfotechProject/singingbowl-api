<?php

namespace Modules\Content\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Media\App\Models\File;
use Modules\Media\Trait\HasMedia;

class Affiliate extends Model
{
    use HasFactory, HasMedia;

    public static $affiliateType = [
        'Partners',
        'Media and coverages',
    ];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'is_partner',
        'title',
        'description',
        'link',
        'is_active'
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

    function getDesktopLogoAttribute()
    {
        return $this->files->where('pivot.zone', 'desktopLogo')->first() ?: new File;
    }

    function getMobileLogoAttribute()
    {
        return $this->files->where('pivot.zone', 'mobileLogo')->first() ?: new File;
    }
}
