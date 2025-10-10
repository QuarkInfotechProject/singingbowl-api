<?php

namespace Modules\SystemConfiguration\Service\Setting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\SystemConfiguration\App\Models\SystemConfig;

class SettingCreateService
{
    function create(array $systemConfig)
    {
        try {
            DB::beginTransaction();

            SystemConfig::firstOrCreate(
                ['name' => $systemConfig['name']],
                [
                    'uuid' => Str::uuid()->toString(),
                    'title' => $systemConfig['title'],
                    'value' => $systemConfig['value'],
                    'section' => $systemConfig['section']
                ]
            );

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error creating system configuration: ' . $exception->getMessage(), [
                'exception' => $exception,
                'systemConfig' => $systemConfig
            ]);
            throw $exception;
        }
    }
}
