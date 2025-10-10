<?php

namespace Modules\Content\Service\Admin\NewLaunch;

use Modules\Content\App\Models\NewLaunch;
use Modules\Content\Trait\GetMediaFilesTrait;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class NewLaunchContentShowService
{
    use GetMediaFilesTrait;

    function show(int $id)
    {
        $newLaunchContent = NewLaunch::select('id', 'link', 'is_active', 'is_banner')
            ->find($id);

        if (!$newLaunchContent) {
            throw new Exception('New launch content not found.', ErrorCode::NOT_FOUND);
        }

        $files = $this->getMediaFiles($newLaunchContent);

        return [
            'link' => $newLaunchContent->link,
            'isActive' => $newLaunchContent->is_active,
            'isBanner' => $newLaunchContent->is_banner,
            'files' => $files
        ];
    }
}
