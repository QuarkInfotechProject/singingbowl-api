<?php

namespace Modules\SystemConfiguration\App\Http\Controllers\Setting;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\SystemConfiguration\Service\Setting\SettingIndexService;

class SettingIndexController extends AdminBaseController
{
    function __construct(private SettingIndexService $settingIndexService)
    {
    }

    function __invoke()
    {
        $settings = $this->settingIndexService->index();

        return $this->successResponse('Setting has been fetched successfully.', $settings);
    }
}
