<?php

namespace Modules\Location\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code'
    ];

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }
}
