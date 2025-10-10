<?php

namespace Modules\Color\App\Http\Controllers;

use Modules\Color\Service\ColorShowService;
use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ColorShowController extends AdminBaseController
{
    public function __construct(private ColorShowService $colorShowService)
    {
    }

    /**
     * Handle the color show request.
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $color = $this->colorShowService->show($request);

        return $this->successResponse([
            'data' => $color,
            'message' => 'Color fetched successfully.'
        ]);
    }
}
