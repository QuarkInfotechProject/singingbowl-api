<?php

namespace Modules\CorporateOrder\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CorporateOrder extends Model
{
    use HasFactory;

    const CANCELLED = 'cancelled';
    const COMPLETED = 'completed';
    const ON_HOLD = 'on_hold';
    const PENDING = 'pending';

    public static $corporateOrderStatusMapping = [
        self::CANCELLED => 'Cancelled',
        self::COMPLETED => 'Completed',
        self::ON_HOLD => 'On Hold',
        self::PENDING => 'Pending'
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'quantity',
        'requirement',
        'status'
    ];
}
