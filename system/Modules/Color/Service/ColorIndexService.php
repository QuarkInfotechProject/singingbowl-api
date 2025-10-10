<?php

namespace Modules\Color\Service;

use Modules\Color\App\Models\Color;

class ColorIndexService
{
    function index()
    {
        return Color::select('id', 'name', 'hex_code as hexCode', 'status')
            ->get();
    }
}
