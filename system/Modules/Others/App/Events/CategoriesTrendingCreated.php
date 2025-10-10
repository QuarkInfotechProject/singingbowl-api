<?php

namespace Modules\Others\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Others\App\Models\CategoriesTrending;

class CategoriesTrendingCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public CategoriesTrending $categoriesTrending
    ) {
    }
}
