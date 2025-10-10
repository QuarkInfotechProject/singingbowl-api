<?php

namespace Modules\SystemConfiguration\App\Http\Controllers\EmailTemplate;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\SystemConfiguration\Service\EmailTemplate\EmailTemplateIndexService;

class EmailTemplateIndexController extends AdminBaseController
{
    function __construct(private EmailTemplateIndexService $emailTemplateIndexService)
    {
    }

    function __invoke()
    {
        $templates = $this->emailTemplateIndexService->index();

        return $this->successResponse('Email Template has been fetched successfully.', $templates);
    }
}
