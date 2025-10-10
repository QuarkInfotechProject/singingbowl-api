<?php

namespace Modules\SystemConfiguration\Service\Setting;

use Modules\SystemConfiguration\App\Models\SystemConfig;

class SettingIndexService
{
    function index()
    {
        return SystemConfig::select('uuid', 'title', 'value', 'section')->get();
    }
}
