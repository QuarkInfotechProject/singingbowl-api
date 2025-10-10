<?php

namespace Modules\Color\Service;

use Modules\Color\App\Models\Color;
use Modules\Color\App\Http\Requests\ColorCreateRequest;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ColorCreateService
{
    /**
     * Create a new color.
     * 
     * @param ColorCreateRequest $request
     * @return Color
     * @throws Exception
     */
    public function create(ColorCreateRequest $request): Color
    {
        $data = $request->validated();

        if (Color::where('name', $data['name'])->exists()) {
            throw new Exception('Color name is already taken.', ErrorCode::CONFLICT);
        }
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $data['hex_code'])) {
            throw new Exception('Invalid hex code format. Must be in #RRGGBB format.', ErrorCode::BAD_REQUEST);
        }

        return Color::create([
            'name' => $data['name'],
            'hex_code' => $data['hex_code'],
            'status' => $data['status'] ?? true,
        ]);
    }
}
