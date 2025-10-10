<?php

namespace Modules\Attribute\Service\AttributeSet;

use Modules\Attribute\App\Models\AttributeSet;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AttributeSetShowService
{
    function show(int $id)
    {
        $attributeSet = AttributeSet::select('id', 'name')
            ->find($id);

        if (!$attributeSet) {
            throw new Exception('Attribute set not found.', ErrorCode::NOT_FOUND);
        }

        return $attributeSet;
    }
}
