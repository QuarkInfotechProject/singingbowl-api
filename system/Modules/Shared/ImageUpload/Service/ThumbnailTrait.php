<?php

namespace Modules\Shared\ImageUpload\Service;

use Exception;
use Modules\Shared\ImageUpload\Exception\ThumbnailImageException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait ThumbnailTrait
{
    /**
     * @param $imagePath
     * @return string
     * @throws ThumbnailImageException
     */
    private function thumbnailImage($imagePath): string
    {
        try {
            $imagick = new \Imagick($imagePath);

            $imagick->resizeImage(200, 200, \Imagick::FILTER_LANCZOS, 1, true);

            $imagick->setImageCompressionQuality(100);

            return $imagick->getImageBlob();


        } catch (\Exception $e) {
            throw new ThumbnailImageException('Failed to create thumbnail: ' . $e->getMessage());
        }
    }

    /**
     * @param $file
     * @return bool
     */
    private function canCreateThumbnail($file): bool
    {
        if (! $file instanceof UploadedFile) {
            return false;
        }
        return (in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']));
    }
}
