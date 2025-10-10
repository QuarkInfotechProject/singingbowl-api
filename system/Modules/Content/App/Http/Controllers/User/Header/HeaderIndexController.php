<?php

namespace Modules\Content\App\Http\Controllers\User\Header;

use Modules\Content\Service\User\Header\HeaderIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class HeaderIndexController extends UserBaseController
{
    function __construct(private HeaderIndexService $headerIndexService)
    {
    }

    function __invoke()
    {
        $contents = $this->headerIndexService->index();

        return $this->successResponse('Header content has been fetched successfully.', $contents);
    }
}
