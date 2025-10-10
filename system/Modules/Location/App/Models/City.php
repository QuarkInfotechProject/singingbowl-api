<?php

namespace Modules\Location\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'province_id'
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }
}
