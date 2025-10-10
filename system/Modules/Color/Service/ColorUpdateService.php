<?php

namespace Modules\Color\Service;

use Modules\Color\App\Models\Color;
use Modules\Color\App\Http\Requests\ColorUpdateRequest;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ColorUpdateService
{
    /**
     * Update the color based on provided id and data.
     * 
     * @param ColorUpdateRequest $request
     * @return \Modules\Color\App\Models\Color
     * @throws Exception
     */
    public function update(ColorUpdateRequest $request): Color
    {
        $data = $request->validated();
        $color = Color::find($data['id']);

        if (Color::where('name', $data['name'])->exists()) {
            throw new Exception('Color name is already taken.', ErrorCode::CONFLICT);
        }
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $data['hex_code'])) {
            throw new Exception('Invalid hex code format. Must be in #RRGGBB format.', ErrorCode::BAD_REQUEST);
        }

        $color->name = $data['name'] ?? $color->name;
        $color->hex_code = $data['hex_code'] ?? $color->hex_code;
        $color->status = isset($data['status']) ? $data['status'] : $color->status;
        $color->save();

        return $color;
    }
}
