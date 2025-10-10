<?php

namespace Modules\Color\App\Http\Controllers;

use Modules\Color\Service\ColorCreateService;
use Modules\Color\App\Http\Requests\ColorCreateRequest;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ColorCreateController extends AdminBaseController
{
    public function __construct(private ColorCreateService $colorCreateService)
    {
    }

    /**
     * Handle the color creation request.
     * 
     * @param ColorCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ColorCreateRequest $request)
    {
        $color = $this->colorCreateService->create($request);

        return $this->successResponse([
            'data' => $color,
            'message' => 'Color has been created successfully.'
        ]);
    }
}
