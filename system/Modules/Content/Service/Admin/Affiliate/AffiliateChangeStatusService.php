<?php

namespace Modules\Content\Service\Admin\Affiliate;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Affiliate;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AffiliateChangeStatusService
{
    function changeStatus(int $id)
    {
        try {
            DB::beginTransaction();

            $affiliate = Affiliate::find($id);

            if (!$affiliate) {
                throw new Exception('Affiliate content not found.', ErrorCode::NOT_FOUND);
            }

            $affiliate->update(['is_active' => !$affiliate['is_active']]);
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to change status of affiliate content.', [
                'error' => $exception->getMessage(),
                'affiliate_id' => $id
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
