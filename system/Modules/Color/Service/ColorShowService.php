<?php

namespace Modules\Color\Service;

use Modules\Color\App\Models\Color;
use Illuminate\Http\Request;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ColorShowService
{
    /**
     * Get the color based on the provided request data.
     * 
     * @param Request $request
     * @return \Modules\Color\App\Models\Color
     * @throws Exception
     */
    public function show(Request $request): Color
    {
        $id = $request->route('id');

        $color = Color::find($id);

        if (!$color) {
            throw new Exception('Color not found.', ErrorCode::NOT_FOUND);
        }

        return $color;
    }
}
