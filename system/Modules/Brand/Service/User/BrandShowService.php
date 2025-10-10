<?php
namespace Modules\Brand\Service\User;

use Illuminate\Http\Request;
use Modules\Brand\App\Models\Brand;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class BrandShowService
{
    public function show(Request $request)
    {
        try {
            $slug = $request->query('slug');

            if (!$slug) {
                throw new Exception('Brand slug is required.', ErrorCode::BAD_REQUEST);
            }

            $brand = Brand::with(['meta', 'files'])
                ->select('slug', 'name', 'status')
                ->where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$brand) {
                $inactiveBrand = Brand::where('slug', $slug)->first(); 
                if ($inactiveBrand) {
                    throw new Exception('This brand is not currently active.', ErrorCode::FORBIDDEN);
                } else {
                    throw new Exception('Brand not found.', ErrorCode::NOT_FOUND);
                }
            }

            return [
                'slug' => $brand->slug,
                'name' => $brand->name,
                'status' => $brand->status,
                'files' => $this->getMediaFiles($brand),
                'meta' => $this->getMetaData($brand->meta()->first()),
            ];
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    private function getMediaFiles(Brand $brand): array
    {
        $mediaFiles = [
            'logo' => null,
            'banner' => null,
        ];

        $logoImageFile = $brand->filterFiles('logoImage')->first();
        if ($logoImageFile) {
            $mediaFiles['logo'] = [
                'id' => $logoImageFile->id,
                'logoUrl' => $logoImageFile->url,
            ];
        }

        $bannerImageFile = $brand->filterFiles('bannerImage')->first();
        if ($bannerImageFile) {
            $mediaFiles['banner'] = [
                'id' => $bannerImageFile->id,
                'bannerUrl' => $bannerImageFile->url,
            ];
        }

        return $mediaFiles;
    }

    private function getMetaData($brandMetaData): array
    {
        return [
            'metaTitle' => $brandMetaData['meta_title'] ?? null,
            'metaDescription' => $brandMetaData['meta_description'] ?? null,
        ];
    }
}