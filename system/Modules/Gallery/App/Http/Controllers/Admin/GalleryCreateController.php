<?php

namespace Modules\Gallery\App\Http\Controllers\Admin;

use Modules\Gallery\App\Http\Requests\Admin\GalleryCreateRequest;
use Modules\Gallery\App\Http\Resources\GalleryResource;
use Modules\Gallery\Service\Admin\GalleryCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class GalleryCreateController extends AdminBaseController
{
    public function __construct(private GalleryCreateService $galleryCreateService)
    {
    }

    public function __invoke(GalleryCreateRequest $request)
    {
        $gallery = $this->galleryCreateService->create($request->validated());

        return $this->successResponse('Gallery created successfully.', new GalleryResource($gallery));
    }
}

