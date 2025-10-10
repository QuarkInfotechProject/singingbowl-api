<?php

namespace Modules\Content\Service\Admin\FlashOffer;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\FlashOffer;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class FlashOfferDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        try {
            $content = FlashOffer::find($id);

            if (!$content) {
                throw new Exception('Flash offer content not found.', ErrorCode::NOT_FOUND);
            }

            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'Flash offer content destroyed of name: ' . $content->name,
                    $content->id,
                    ActivityTypeConstant::FLASH_OFFER_CONTENT_DESTROYED,
                    $ipAddress
                )
            );

            $content->delete();
        } catch (\Exception $exception) {
            Log::error('Failed to destroy flash offer content.', [
                'error' => $exception->getMessage(),
                'id' => $id
            ]);
            throw $exception;
        }
    }
}
