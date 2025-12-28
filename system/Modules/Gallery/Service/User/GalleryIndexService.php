<?php

namespace Modules\Gallery\Service\User;

use Illuminate\Support\Collection;
use Modules\Gallery\App\Models\Gallery;

class GalleryIndexService
{
    public function list(): Collection
    {
        return Gallery::query()
            ->with('files')
            ->latest()
            ->get();
    }
}

