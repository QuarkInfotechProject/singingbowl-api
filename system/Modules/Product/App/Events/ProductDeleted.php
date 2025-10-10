<?php

namespace Modules\Product\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductDeleted
{
    use Dispatchable, SerializesModels;

    public int|string $productId;

    public function __construct(int|string $productId)
    {
        $this->productId = $productId;
    }
}
