<?php

namespace Modules\SystemConfiguration\App\Http\Controllers\Setting;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\SystemConfiguration\App\Http\Requests\UpdateSystemConfigSettingRequest;
use Modules\SystemConfiguration\Service\Setting\SettingUpdateService;

class SettingUpdateController extends AdminBaseController
{
    function __construct(private SettingUpdateService $settingUpdateService)
    {
    }

    function __invoke(UpdateSystemConfigSettingRequest $request)
    {
        $this->settingUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Settings has been updated successfully.');
    }
}
