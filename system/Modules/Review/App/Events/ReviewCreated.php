<?php

namespace Modules\Review\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Review\App\Models\Review;

class ReviewCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Review $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }
}
