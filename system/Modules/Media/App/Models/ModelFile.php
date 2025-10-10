<?php

namespace Modules\Media\App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelFile extends Model
{
    protected $table = 'model_files';
    protected $guarded = [];

    public function model()
    {
        return $this->morphTo();
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
