<?php

namespace Modules\Content\Service\Admin\BestSeller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\BestSeller;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class BestSellerChangeStatusService
{
    function changeStatus(int $id)
    {
        try {
            DB::beginTransaction();

            $content = BestSeller::find($id);

            if (!$content) {
                throw new Exception('Best seller content not found.', ErrorCode::NOT_FOUND);
            }

            $content->update(['is_active' => !$content['is_active']]);
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to change status of best seller content.', [
                'error' => $exception->getMessage(),
                'id' => $id
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
