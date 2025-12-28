
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
        $galleries = $this->galleryIndexService->list(request()->all());

        return $this->successResponse(
            'Gallery list fetched successfully.',
            [
                'data' => GalleryResource::collection($galleries->items()),
                'pagination' => [
                    'current_page' => $galleries->currentPage(),
                    'last_page' => $galleries->lastPage(),
                    'per_page' => $galleries->perPage(),
                    'total' => $galleries->total(),
                    'from' => $galleries->firstItem(),
                    'to' => $galleries->lastItem(),
                ]
            ]
        );
    }
}

