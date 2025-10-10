<?php

namespace Modules\Content\Service\Admin\FlashOffer;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\FlashOffer;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class FlashOfferChangeStatusService
{
    function changeStatus(int $id)
    {
        try {
            DB::beginTransaction();

            $content = FlashOffer::find($id);

            if (!$content) {
                throw new Exception('Flash offer content not found.', ErrorCode::NOT_FOUND);
            }

            // If trying to activate an inactive flash offer
            if (!$content->is_active) {
                $activeCount = FlashOffer::where('is_active', true)->count();
                
                // If already 2 active flash offers, throw exception
                if ($activeCount >= 2) {
                    throw new Exception('Cannot activate flash offer. Maximum 2 flash offers can be active at a time.', ErrorCode::BAD_REQUEST);
                }
            }

            $content->update(['is_active' => !$content['is_active']]);
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to change status of flash offer content.', [
                'error' => $exception->getMessage(),
                'id' => $id
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
