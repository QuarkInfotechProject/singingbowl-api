<?php

namespace Modules\Content\Service\Admin\FlashOffer;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\FlashOffer;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class FlashOfferUpdateService
{
    function update($data, string $ipAddress)
    {
        $content = FlashOffer::find($data['id']);

        if (!$content) {
            throw new Exception('Flash offer content not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            // Check if user wants to make flash offer active
            $isActive = $data['is_active'] ?? $content->is_active;
            
            // If trying to activate and currently inactive
            if ($isActive && !$content->is_active) {
                $activeCount = FlashOffer::where('is_active', true)->count();
                
                // If already 2 active flash offers, mark this one as inactive
                if ($activeCount >= 2) {
                    $isActive = false;
                }
            }

            $content->update([
                'name' => $data['name'],
                'link' => $data['link'],
                'is_active' => $isActive,
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update flash offer content.', [
                'error' => $exception->getMessage(),
                'data' => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Flash offer content updated.',
                $content->id,
                ActivityTypeConstant::FLASH_OFFER_CONTENT_UPDATED,
                $ipAddress)
        );
    }
}
