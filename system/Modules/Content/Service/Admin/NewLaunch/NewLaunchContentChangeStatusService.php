<?php

namespace Modules\Content\Service\Admin\NewLaunch;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\NewLaunch;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class NewLaunchContentChangeStatusService
{
    function changeStatus(int $id)
    {
        try {
            DB::beginTransaction();

            $newLaunchContent = NewLaunch::find($id);

            if (!$newLaunchContent) {
                throw new Exception('New launch content not found.', ErrorCode::NOT_FOUND);
            }

            $newLaunchContent->update(['is_active' => !$newLaunchContent['is_active']]);
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to change status for NewLaunch content: ' . $exception->getMessage(), [
                'exception' => $exception,
                'id' => $id
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
