<?php

namespace Modules\Brand\Service\Admin;

use Illuminate\Pagination\Paginator;
use Modules\Brand\App\Models\Brand;

class BrandIndexService
{

    public function index()
    {
        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 50);

        Paginator::currentPageResolver(fn () => $page);

        return Brand::select('id', 'name','slug', 'status')
            ->latest()
            ->paginate($perPage)
            ->through(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'status' => $brand->status,
                    'logo' => $this->getMediaFileUrl($brand, 'logoImage'),
                    'banner' => $this->getMediaFileUrl($brand, 'bannerImage'),
                ];
            });
    }

    private function getMediaFileUrl(Brand $brand, string $type): ?array
    {
        $file = $brand->filterFiles($type)->first();

        if ($file) {
            return [
                'id' => $file->id,
                'url' => $file->path . '/' . $file->temp_filename,
            ];
        }

        return null;
    }
}
