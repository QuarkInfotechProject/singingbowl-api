<?php

namespace Modules\Content\Service\Admin\InThePress;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\InThePress;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class InThePressChangeStatusService
{
    function changeStatus(int $id)
    {
        try {
            DB::beginTransaction();

            $content = InThePress::find($id);

            if (!$content) {
                throw new Exception('Content not found.', ErrorCode::NOT_FOUND);
            }

            $content->update(['is_active' => !$content['is_active']]);
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to change status for InThePress content: ' . $exception->getMessage(), [
                'exception' => $exception,
                'id' => $id
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
