<?php

namespace Modules\Gallery\App\Http\Controllers\User;

use Modules\Gallery\App\Http\Resources\GalleryResource;
use Modules\Gallery\Service\User\GalleryIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class GalleryIndexController extends AdminBaseController
{
    public function __construct(private GalleryIndexService $galleryIndexService)
    {
    }

    public function __invoke()
    {
        $galleries = $this->galleryIndexService->list();

        return $this->successResponse(
            'Gallery list fetched successfully.',
            GalleryResource::collection($galleries)
        );
    }
}

