<?php
namespace Modules\Brand\Service\Admin;

use Modules\Brand\App\Models\Brand;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class BrandShowService
{
    public function show(int $id)
    {
        try {
            $brand = Brand::select('id', 'name','slug', 'status')
                ->find($id);

            if (!$brand) {
                throw new Exception('Brand not found.', ErrorCode::NOT_FOUND);
            }

            $mediaFiles = $this->getMediaFiles($brand);
            $metaData = $this->getMetaData($brand);

            return [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'status' => $brand->status,
                'files' => $mediaFiles,
                'meta' => $metaData,
            ];

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    private function getMediaFiles(Brand $post): ?array
    {
        $mediaFiles = [
            'logo' => null,
            'banner' => null,
        ];

        $logoImageFile = $post->filterFiles('logoImage')->first();
        $bannerImageFile = $post->filterFiles('bannerImage')->first();

        if ($logoImageFile) {
            $mediaFiles['logo'] = [
                'id' => $logoImageFile->id,
                'logoUrl' => $logoImageFile->path . '/' . $logoImageFile->temp_filename,
            ];
        }

        if ($bannerImageFile) {
            $mediaFiles['banner'] = [
                'id' => $bannerImageFile->id,
                'bannerUrl' => $bannerImageFile->path . '/' . $bannerImageFile->temp_filename,
            ];
        }

        return $mediaFiles;
    }


    private function getMetaData(Brand $post)
    {
        $postMetaData = $post->meta()->first();

        return [
            'metaTitle' => $postMetaData['meta_title'],
            'metaDescription' => $postMetaData['meta_description'],
        ];
    }


}
