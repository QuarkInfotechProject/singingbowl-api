<?php

namespace Modules\Content\App\Http\Controllers\Admin\Header;

use Modules\Content\App\Http\Requests\Header\HeaderCreateRequest;
use Modules\Content\Service\Admin\Header\HeaderCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class HeaderCreateController extends AdminBaseController
{
    function __construct(private HeaderCreateService $headerCreateService)
    {
    }

    function __invoke(HeaderCreateRequest $request)
    {
        $this->headerCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('Header content has been created successfully.');
    }
}
