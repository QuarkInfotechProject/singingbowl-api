<?php

namespace Modules\Others\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoriesTrendingDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $categoriesTrendingId
    ) {
    }
}
