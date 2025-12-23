<?php

namespace Modules\Gallery\Service\User;

use Modules\Gallery\App\Models\Gallery;

class GalleryShowService
{
    public function showBySlug(string $slug): Gallery
    {
        return Gallery::where('slug', $slug)
            ->where('status', true)
            ->with('files')
            ->firstOrFail();
    }
}

