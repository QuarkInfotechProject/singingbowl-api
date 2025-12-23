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
            ->when(isset($filters['status']), fn ($query) => $query->where('status', (bool) $filters['status']))
            ->when(!empty($filters['search']), function ($query) use ($filters) {
                $query->where('title', 'like', '%'.$filters['search'].'%')
                    ->orWhere('slug', 'like', '%'.$filters['search'].'%');
            })
            ->with('files')
            ->latest()
            ->paginate($perPage);
    }
}

