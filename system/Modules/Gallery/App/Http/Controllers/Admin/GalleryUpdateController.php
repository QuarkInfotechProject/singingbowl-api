<?php

namespace Modules\Gallery\App\Http\Controllers\Admin;

use Modules\Gallery\App\Http\Requests\Admin\GalleryUpdateRequest;
use Modules\Gallery\App\Http\Resources\GalleryResource;
use Modules\Gallery\Service\Admin\GalleryUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class GalleryUpdateController extends AdminBaseController
{
    public function __construct(private GalleryUpdateService $galleryUpdateService)
    {
    }

    public function __invoke(GalleryUpdateRequest $request)
    {
        $gallery = $this->galleryUpdateService->update($request->validated());

        return $this->successResponse('Gallery updated successfully.', new GalleryResource($gallery));
    }
}

