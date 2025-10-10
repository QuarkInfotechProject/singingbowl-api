<?php

namespace Modules\Content\App\Http\Controllers\Admin\Header;

use Modules\Content\App\Http\Requests\Header\HeaderUpdateRequest;
use Modules\Content\Service\Admin\Header\HeaderUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class HeaderUpdateController extends AdminBaseController
{
    function __construct(private HeaderUpdateService $headerUpdateService)
    {
    }

    function __invoke(HeaderUpdateRequest $request)
    {
        $this->headerUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Header content has been updated successfully.');
    }
}
