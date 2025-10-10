<?php

namespace Modules\Attribute\Service\AttributeSet;

use Modules\Attribute\App\Models\AttributeSet;

class AttributeSetIndexService
{
    function index()
    {
        $perPage = request()->query('perPage', 25);

        return AttributeSet::select('id', 'name', 'created_at as created')
            ->latest()
            ->paginate($perPage);
    }
}
