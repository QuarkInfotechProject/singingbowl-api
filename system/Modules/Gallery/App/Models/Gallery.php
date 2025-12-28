<?php

namespace Modules\Gallery\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Media\Trait\HasMedia;
use Illuminate\Support\Str;

class Gallery extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'uuid',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Gallery $gallery) {
            if (empty($gallery->uuid)) {
                $gallery->uuid = (string) Str::uuid();
            }
        });
    }
}

