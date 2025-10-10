<?php

namespace Modules\Content\Service\Admin\FlashOffer;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\FlashOffer;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class FlashOfferCreateService
{
    function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            // Check if user wants to create active flash offer
            $isActive = $data['is_active'] ?? true;
            
            // If trying to create active flash offer, check current active count
            if ($isActive) {
                $activeCount = FlashOffer::where('is_active', true)->count();
                
                // If already 2 active flash offers, mark this one as inactive
                if ($activeCount >= 2) {
                    $isActive = false;
                }
            }

            $content = FlashOffer::create([
                'name' => $data['name'],
                'link' => $data['link'],
                'is_active' => $isActive,
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create flash offer content.', [
                'error' => $exception->getMessage(),
                'data' => $data
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Flash offer content added of name: ' . $content->name,
                $content->id,
                ActivityTypeConstant::FLASH_OFFER_CONTENT_CREATED,
                $ipAddress)
        );
    }
}
