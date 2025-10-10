<?php

namespace Modules\Color\Service;

use Modules\Color\App\Models\Color;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ColorChangeStatusService
{
    function changeStatus(int $id)
    {
        $color = Color::find($id);

        if (!$color) {
            throw new Exception('Color not found.', ErrorCode::NOT_FOUND);
        }

        $color->update(['status' => !$color->status]);
    }
}
