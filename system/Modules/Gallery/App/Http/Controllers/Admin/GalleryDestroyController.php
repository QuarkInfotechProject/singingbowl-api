<?php

namespace Modules\Gallery\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Gallery\Service\Admin\GalleryDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class GalleryDestroyController extends AdminBaseController
{
    public function __construct(private GalleryDestroyService $galleryDestroyService)
    {
    }

    public function __invoke(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:galleries,id'],
        ]);

        $this->galleryDestroyService->destroy($request->get('id'));

        return $this->successResponse('Gallery deleted successfully.');
    }
}

