<?php

namespace Modules\Gallery\Service\User;

use Illuminate\Support\Collection;
use Modules\Gallery\App\Models\Gallery;

class GalleryIndexService
{
    public function list(): Collection
    {
        $gallery = Gallery::first();

        if (!$gallery) {
            return collect([]);
        }

        return $gallery->files()->latest()->get();
    }
}

