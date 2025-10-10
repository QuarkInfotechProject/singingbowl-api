<?php
namespace Modules\Brand\Service\User;

use Illuminate\Pagination\Paginator;
use Modules\Brand\App\Models\Brand;

class BrandIndexService
{
    public function index($status = null, $name = null, $page = 1)
    {
        $perPage = request()->get('per_page', 20);
        Paginator::currentPageResolver(fn () => $page);

        $query = Brand::with('files')
            ->select('id', 'name', 'slug', 'status')
            ->where('status', 1)
            ->latest();

        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage)
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
        if (!$file) {
            return null;
        }

        return [
            'id' => $file->id,
            'url' => $file->url,
        ];
    }
}