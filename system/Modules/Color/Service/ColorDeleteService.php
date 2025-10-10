<?php

namespace Modules\Color\Service;

use Modules\Color\App\Models\Color;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ColorDeleteService
{
    /**
     * Delete the color by its id.
     * 
     * @param int|null $id
     * @return bool
     * @throws Exception
     */
    public function delete(?int $id): bool
    {
        if (!$id) {
            throw new Exception('ID is required.', ErrorCode::BAD_REQUEST);
        }

        $color = Color::find($id);

        if (!$color) {
            throw new Exception('Color not found.', ErrorCode::NOT_FOUND);
        }

        return $color->delete();
    }
}
