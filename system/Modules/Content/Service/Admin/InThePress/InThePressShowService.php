<?php

namespace Modules\Content\Service\Admin\InThePress;

use Modules\Content\App\Models\InThePress;
use Modules\Content\Trait\GetMediaFilesTrait;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class InThePressShowService
{
    use GetMediaFilesTrait;
    function show(int $id)
    {
        $content = InThePress::select('id', 'title', 'link', 'published_date')
            ->find($id);

        if (!$content) {
            throw new Exception('Content not found.', ErrorCode::NOT_FOUND);
        }

        $files = $this->getMediaFiles($content);

        $content->makeHidden(['id']);

        return [
            'title' => $content->title,
            'link' => $content->link,
            'publishedDate' => $content->published_date,
            'files' => $files
        ];
    }
}
