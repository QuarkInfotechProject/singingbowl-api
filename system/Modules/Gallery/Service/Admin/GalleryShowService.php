<?php

namespace Modules\Gallery\Service\Admin;

use Modules\Gallery\App\Models\Gallery;

class GalleryShowService
{
    public function show(int $id): Gallery
    {
        return Gallery::with('files')->findOrFail($id);
    }
}

