<?php

namespace Modules\Gallery\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Gallery\App\Models\Gallery;

class GalleryUpdateService
{
    public function update(array $data): Gallery
    {
        return DB::transaction(function () use ($data) {
            /** @var Gallery $gallery */
            $gallery = Gallery::findOrFail($data['id']);

            $gallery->fill([
                'title' => $data['title'],
                'slug' => $data['slug'] ?? $gallery->slug ?? Str::slug($data['title']),
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? $gallery->status,
            ])->save();

            if (array_key_exists('images', $data)) {
                $gallery->syncFiles(['galleryImage' => $data['images'] ?? []]);
            }

            return $gallery->refresh();
        });
    }
}

