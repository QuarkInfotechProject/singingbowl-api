<?php

namespace Modules\Media\Service\FileCategory;

use Modules\Media\App\Models\FileCategory;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class FileCategoryShowService
{
    function show(string $slug)
    {
        $fileCategory =  FileCategory::where('slug', $slug)
            ->select('name', 'slug as url')
            ->first();

        if (!$fileCategory) {
            throw new Exception('File category not found.', ErrorCode::NOT_FOUND);
        }

        return $fileCategory;
    }
}
