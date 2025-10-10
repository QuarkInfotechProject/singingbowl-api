<?php

namespace Modules\Review\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewReply extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['review_id', 'content'];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
