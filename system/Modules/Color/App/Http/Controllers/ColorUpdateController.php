<?php

namespace Modules\Color\App\Http\Controllers;

use Modules\Color\Service\ColorUpdateService;
use Modules\Color\App\Http\Requests\ColorUpdateRequest;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ColorUpdateController extends AdminBaseController
{
    public function __construct(private ColorUpdateService $colorUpdateService)
    {
    }

    /**
     * Handle the color update request.
     * 
     * @param ColorUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ColorUpdateRequest $request)
    {
        $color = $this->colorUpdateService->update($request);

        return $this->successResponse([
            'data' => $color,
            'message' => 'Color has been updated successfully.'
        ]);
    }
}
