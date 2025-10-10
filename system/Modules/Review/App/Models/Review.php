<?php

namespace Modules\Review\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\App\Models\Product;
use Modules\User\App\Models\User;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    public const QUESTION = 'question';
    public const REVIEW = 'review';

    /**
     * @var string[]
     */
    public static $feedbackType = [
        self::QUESTION => 'Question',
        self::REVIEW => 'Review',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'product_id',
        'type',
        'name',
        'email',
        'rating',
        'comment',
        'images',
        'is_approved',
        'is_replied',
        'ip_address'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_replied' => 'boolean'
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function replies()
    {
        return $this->hasOne(ReviewReply::class);
    }
}
