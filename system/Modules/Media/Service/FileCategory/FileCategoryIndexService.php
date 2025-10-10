<?php

namespace Modules\Media\Service\FileCategory;

use Modules\Media\App\Models\FileCategory;

class FileCategoryIndexService
{
    function index()
    {
        return FileCategory::select('id', 'name', 'slug as url')->get();
    }
}
