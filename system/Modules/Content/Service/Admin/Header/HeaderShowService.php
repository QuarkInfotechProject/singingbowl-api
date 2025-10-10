<?php

namespace Modules\Content\Service\Admin\Header;

use Modules\Content\App\Models\Header;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class HeaderShowService
{
    function show(int $id)
    {
        $content = Header::select('text', 'link', 'is_active as isActive')
                    ->find($id);

        if (!$content) {
            throw new Exception('Header content not found.', ErrorCode::NOT_FOUND);
        }

        return $content;
    }
}
