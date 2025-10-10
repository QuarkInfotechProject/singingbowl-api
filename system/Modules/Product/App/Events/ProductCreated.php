<?php

namespace Modules\Product\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Product\App\Models\Product;

class ProductCreated
{
    use Dispatchable, SerializesModels;

    public Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
