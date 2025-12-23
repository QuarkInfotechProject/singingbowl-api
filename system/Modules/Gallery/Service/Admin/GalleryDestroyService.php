<?php

namespace Modules\Gallery\Service\Admin;

use Illuminate\Support\Facades\DB;
use Modules\Gallery\App\Models\Gallery;

class GalleryDestroyService
{
    public function destroy(int $id): void
    {
        DB::transaction(function () use ($id) {
            $gallery = Gallery::findOrFail($id);
            $gallery->filterFiles('galleryImage')->detach();
            $gallery->delete();
        });
    }
}

