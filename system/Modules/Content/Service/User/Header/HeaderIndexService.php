<?php

namespace Modules\Content\Service\User\Header;

use Modules\Content\App\Models\Header;

class HeaderIndexService
{
    function index()
    {
        return Header::select('text', 'link')
            ->where('is_active', true)
            ->latest()
            ->get();
    }
}
