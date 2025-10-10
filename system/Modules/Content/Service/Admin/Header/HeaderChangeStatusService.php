<?php

namespace Modules\Content\Service\Admin\Header;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Header;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class HeaderChangeStatusService
{
    function changeStatus(int $id)
    {
        $content = Header::find($id);

        if (!$content) {
            throw new Exception('Header content not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

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
