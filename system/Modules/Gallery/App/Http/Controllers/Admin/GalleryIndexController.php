<?php

namespace Modules\Gallery\App\Http\Controllers\Admin;

use Modules\Gallery\App\Http\Resources\GalleryResource;
use Modules\Gallery\Service\Admin\GalleryIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class GalleryIndexController extends AdminBaseController
{
    public function __construct(private GalleryIndexService $galleryIndexService)
    {
    }

    public function __invoke()
    {
        $galleries = $this->galleryIndexService->list(request()->all());

        return $this->successResponse(
            'Gallery list fetched successfully.',
            GalleryResource::collection($galleries)
        );
    }
}

