<?php

namespace Modules\Media\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;

    protected $hidden = ['pivot'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'file_category_id',
        'is_grouped',
        'filename',
        'temp_filename',
        'alternative_text',
        'title',
        'caption',
        'description',
        'disk',
        'path',
        'extension',
        'mime',
        'size',
        'width',
        'height'
    ];

    public function fileCategory()
    {
        return $this->belongsTo(FileCategory::class);
    }

    public function file()
    {
        return $this->has(ModelFile::class);
    }

    public function getUrlAttribute()
    {
        if (!empty($this->path) && !empty($this->temp_filename)) {
            return $this->path . '/' . $this->temp_filename;
        }

        return null;
    }
}
