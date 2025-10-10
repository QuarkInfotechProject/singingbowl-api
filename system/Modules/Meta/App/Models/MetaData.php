<?php

namespace Modules\Meta\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MetaData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['entity_id', 'entity_type', 'meta_title', 'meta_keywords', 'meta_description'];
}
