<?php

namespace Modules\AdminUser\App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Order\App\Models\OrderLog;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Support\Str;

class AdminUser extends Authenticatable
{
    use HasRoles, HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    // Use default auto-incrementing integer primary key
    public $incrementing = true;
    protected $keyType = 'int';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // Generate UUID for the uuid column (not the primary key)
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public const ACTIVE = 1;
    public const INACTIVE = 2;
    public const DELETED = 3;

    /**
     * @var string[]
     */
    public static $adminUserStatus = [
        self::ACTIVE => 'Active',
        self::INACTIVE => 'Inactive',
        self::DELETED => 'Deleted',
    ];

    protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'super_admin',
        'email',
        'password',
        'status',
        'remarks'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orderLog()
    {
        return $this->hasMany(OrderLog::class);
    }
}
