<?php

namespace Modules\Content\Service\Admin\Content;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Content;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ContentChangeStatusService
{
    function changeStatus(int $id)
    {
        try {
            DB::beginTransaction();

            $content = Content::find($id);

            if (!$content) {
                throw new Exception('Content not found.', ErrorCode::NOT_FOUND);
            }

            $content->update(['is_active' => !$content['is_active']]);
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to change content status.', [
                'error' => $exception->getMessage(),
                'id' => $id
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
