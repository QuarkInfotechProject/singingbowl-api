<?php

namespace Modules\OrderProcessing\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderArtifact extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'date',
        'file_name',
        'file_path',
        'order_count',
        'shipping_company'
    ];
}
