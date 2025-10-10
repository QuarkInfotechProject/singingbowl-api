<?php

namespace Modules\SystemConfiguration\App\Http\Controllers\EmailTemplate;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\SystemConfiguration\Service\EmailTemplate\EmailTemplateShowService;

class EmailTemplateShowController extends AdminBaseController
{
    function __construct(private EmailTemplateShowService $emailTemplateShowService)
    {
    }

    function __invoke(string $name)
    {
        $emailTemplate = $this->emailTemplateShowService->show($name);

        return $this->successResponse('Email template has been fetched successfully.', $emailTemplate);
    }
}
