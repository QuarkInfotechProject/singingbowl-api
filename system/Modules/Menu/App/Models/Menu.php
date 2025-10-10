<?php

namespace Modules\Menu\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Menu\Database\factories\MenuFactory;

class Menu extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'parent_id',
        'title',
        'url',
        'icon',
        'sort_order',
        'status'
    ];

    protected static function newFactory(): MenuFactory
    {
        //return MenuFactory::new();
    }
}
