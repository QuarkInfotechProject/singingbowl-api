<?php

namespace Modules\Order\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Address\App\Models\Address;
use Modules\User\App\Models\User;

class OrderAddress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'address_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
