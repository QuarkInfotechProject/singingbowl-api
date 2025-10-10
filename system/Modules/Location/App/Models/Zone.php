<?php

namespace Modules\Location\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zone extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'city_id'
    ];

    public function cities()
    {
        return $this->belongsTo(City::class);
    }
}
