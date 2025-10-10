<?php

namespace Modules\SystemConfiguration\Service\Setting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\SystemConfig;

class SettingUpdateService
{
    function update($systemConfig, $ipAddress)
    {
        $settings = SystemConfig::where('section', $systemConfig['section'])->pluck('uuid')->toArray();

        foreach ($systemConfig['configs'] as $setting) {
            $unMatchedSettings = array_diff([$setting['id']], $settings);

            if (!empty($unMatchedSettings)) {
                throw new Exception("System setting not found for section: {$systemConfig['section']}", ErrorCode::NOT_FOUND);
            }
        }

        try {
            DB::beginTransaction();
            foreach ($systemConfig['configs'] as $setting) {
                SystemConfig::where('uuid', $setting['id'])
                    ->update([
                        'value' => $setting['value']
                    ]);
            }

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error updating system configuration: ' . $exception->getMessage(), [
                'exception' => $exception,
                'systemConfig' => $systemConfig,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Setting updated of section: ' . $systemConfig['section'],
                null,
                ActivityTypeConstant::SETTING_UPDATED,
                $ipAddress)
        );
    }
}
