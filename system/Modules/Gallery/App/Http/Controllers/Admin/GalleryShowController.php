<?php

namespace Modules\Gallery\App\Http\Controllers\Admin;

use Modules\Gallery\App\Http\Resources\GalleryResource;
use Modules\Gallery\Service\Admin\GalleryShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class GalleryShowController extends AdminBaseController
{
    public function __construct(private GalleryShowService $galleryShowService)
    {
    }

    public function __invoke(int $id)
    {
        $gallery = $this->galleryShowService->show($id);

        return $this->successResponse('Gallery fetched successfully.', new GalleryResource($gallery));
    }
}

