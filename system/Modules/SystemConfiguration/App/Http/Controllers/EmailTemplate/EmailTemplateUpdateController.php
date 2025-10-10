<?php

namespace Modules\SystemConfiguration\App\Http\Controllers\EmailTemplate;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\SystemConfiguration\App\Http\Requests\UpdateEmailTemplateRequest;
use Modules\SystemConfiguration\DTO\UpdateEmailTemplateDTO;
use Modules\SystemConfiguration\Service\EmailTemplate\EmailTemplateUpdateService;

class EmailTemplateUpdateController extends AdminBaseController
{
    function __construct(private EmailTemplateUpdateService $emailTemplateUpdateService)
    {
    }

    function __invoke(UpdateEmailTemplateRequest $request)
    {
        $updateEmailTemplateDTO = UpdateEmailTemplateDTO::from($request->all());

        $this->emailTemplateUpdateService->update($updateEmailTemplateDTO, $request->getClientIp());

        return $this->successResponse('Email template has been updated successfully.');
    }
}
