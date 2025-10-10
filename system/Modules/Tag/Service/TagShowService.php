<?php

namespace Modules\Tag\Service;

use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Tag\App\Models\Tag;

class TagShowService
{
    function show(int $id)
    {
        $tag = Tag::select('id', 'name', 'slug as url')
            ->find($id);

        if (!$tag) {
            throw new Exception('Tag not found.', ErrorCode::NOT_FOUND);
        }

        return $tag;
    }
}
