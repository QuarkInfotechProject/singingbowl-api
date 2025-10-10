<?php

namespace Modules\Others\Service\Feature;

use Modules\Others\App\Models\Features;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class FeatureShowService
{
    function show(int $id)
    {
        $feature = Features::select('id', 'text', 'is_active')
            ->find($id);

        if (!$feature) {
            throw new Exception('Feature not found.', ErrorCode::NOT_FOUND);
        }

        return [
            'text' => $feature->text,
            'isActive' => $feature->is_active,
        ];
    }
}
