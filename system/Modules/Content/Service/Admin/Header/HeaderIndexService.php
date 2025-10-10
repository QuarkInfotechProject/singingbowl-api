<?php

namespace Modules\Content\Service\Admin\Header;

use Modules\Content\App\Models\Header;

class HeaderIndexService
{
    function index()
    {
        return Header::select('id', 'text', 'link', 'is_active as isActive')->get();
    }
}
