<?php

namespace Modules\Support\Service\Admin\GeneralSupport;

use Modules\Support\App\Models\GeneralSupport;

class GeneralSupportIndexService
{
    function index()
    {
        return GeneralSupport::select('id', 'name', 'email', 'phone')
            ->get();
    }
}
