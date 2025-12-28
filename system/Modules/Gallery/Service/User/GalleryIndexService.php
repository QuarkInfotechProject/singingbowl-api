<?php

namespace Modules\Gallery\Service\User;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Gallery\App\Models\Gallery;

class GalleryIndexService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['perPage'] ?? 25;
        $gallery = Gallery::first();

        if (!$gallery) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
        }

        return $gallery->files()->latest()->paginate($perPage);
    }
}

