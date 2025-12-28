<?php

namespace Modules\Gallery\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Gallery\App\Models\Gallery;

class GalleryCreateService
{
    public function create(array $data): Gallery
    {
        return DB::transaction(function () use ($data) {
            if (Gallery::count() >= 1) {
                throw new \RuntimeException('Only one gallery is allowed. Please update the existing gallery.');
            }

            $gallery = Gallery::create([
                'uuid' => Str::uuid(),
            ]);

            if (!empty($data['images'])) {
                $gallery->syncFiles(['galleryImage' => $data['images']]);
            }

            return $gallery->refresh();
        });
    }
}

