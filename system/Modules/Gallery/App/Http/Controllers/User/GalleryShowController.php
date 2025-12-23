<?php

namespace Modules\Gallery\App\Http\Controllers\User;

use Modules\Gallery\App\Http\Resources\GalleryResource;
use Modules\Gallery\Service\User\GalleryShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class GalleryShowController extends AdminBaseController
{
    public function __construct(private GalleryShowService $galleryShowService)
    {
    }

    public function __invoke(string $slug)
    {
        $gallery = $this->galleryShowService->showBySlug($slug);

        return $this->successResponse('Gallery fetched successfully.', new GalleryResource($gallery));
    }
}

