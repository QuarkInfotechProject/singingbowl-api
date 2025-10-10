<?php

namespace Modules\Review\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $productId;

    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }
}
