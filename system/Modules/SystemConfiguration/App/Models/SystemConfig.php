<?php

namespace Modules\SystemConfiguration\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemConfig extends Model
{
    use HasFactory;

    protected $table = 'system_config_settings';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'name',
        'title',
        'value',
        'section'
    ];

}
