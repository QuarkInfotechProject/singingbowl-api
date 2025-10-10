<?php

namespace Modules\Order\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AdminUser\App\Models\AdminUser;

class OrderLog extends Model
{
    use HasFactory;

    public const DEFAULT_NOTE = 1;
    public const PERSONAL_NOTE = 2;
    public const CUSTOMER_NOTE = 3;

    public static $noteType = [
        self::DEFAULT_NOTE => 'Default Note',
        self::PERSONAL_NOTE => 'Personal Note',
        self::CUSTOMER_NOTE => 'Customer Note',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'description',
        'order_id',
        'modifier_id',
        'note_type',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function modifier()
    {
        return $this->belongsTo(AdminUser::class);
    }
}
