<?php

namespace Modules\Gallery\Service\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Gallery\App\Models\Gallery;

class GalleryIndexService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['perPage'] ?? 15;

        return Gallery::query()
            ->with('files')
            ->latest()
            ->paginate($perPage);
    }
}

