<?php

namespace Modules\Color\App\Http\Controllers;

use Modules\Color\Service\ColorDeleteService;
use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ColorDeleteController extends AdminBaseController
{
    public function __construct(private ColorDeleteService $colorDeleteService)
    {
    }

    /**
     * Handle the color delete request.
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->colorDeleteService->delete($request->input('id'));

        return $this->successResponse([
            'message' => 'Color has been deleted successfully.'
        ]);
    }
}
