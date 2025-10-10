<?php

namespace Modules\Media\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    public function files()
    {
        return $this->hasMany(File::class, 'file_category_id');
    }
}
