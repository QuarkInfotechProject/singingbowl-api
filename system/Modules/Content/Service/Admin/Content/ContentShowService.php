<?php

namespace Modules\Content\Service\Admin\Content;

use Modules\Content\App\Models\Content;
use Modules\Content\Trait\GetMediaFilesTrait;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ContentShowService
{
    use GetMediaFilesTrait;

    function show(int $id)
    {
        $content = Content::select('id', 'link', 'is_active', 'type')
            ->find($id);

        if (!$content) {
            throw new Exception('Content not found.', ErrorCode::NOT_FOUND);
        }

        $files = $this->getMediaFiles($content);

        return [
            'link' => $content->link,
            'isActive' => $content->is_active,
            'type' => $content->type,
            'files' => $files
        ];
    }
}
