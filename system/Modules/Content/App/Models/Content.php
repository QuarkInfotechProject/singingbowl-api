<?php

namespace Modules\Content\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Media\App\Models\File;
use Modules\Media\Trait\HasMedia;

class Content extends Model
{
    use HasFactory, HasMedia;

    public const HERO_SECTION_SLIDER = 1;
    public const OFFER_BANNER = 2;
    public const POP_UP_SECTION_BANNER = 3;
    public const HERO_SECTION_MINI = 5;
    public const HERO_SECTION_LONG = 6;

    /**
     * @var string[]
     */
    public static $contentType = [
        self::HERO_SECTION_SLIDER => 'Hero section slider',
        self::OFFER_BANNER => 'Offer section banner',
        self::POP_UP_SECTION_BANNER => 'Pop up section banner',
        self::HERO_SECTION_MINI => 'Hero section mini',
        self::HERO_SECTION_LONG => 'Hero section long'
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'link',
        'is_active',
        'type'
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

    function getDesktopImageAttribute()
    {
        return $this->files->where('pivot.zone', 'desktopImage')->first() ?: new File;
    }

    function getMobileImageAttribute()
    {
        return $this->files->where('pivot.zone', 'mobileImage')->first() ?: new File;
    }
}
