<?php

namespace Modules\Content\Service\Admin\Affiliate;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Affiliate;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AffiliateDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        try {
            $affiliate = Affiliate::find($id);

            if (!$affiliate) {
                throw new Exception('Affiliate content not found.', ErrorCode::NOT_FOUND);
            }

            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'Affiliate content destroyed of type: ' . ($affiliate->is_partner ?
                        Affiliate::$affiliateType[0] :
                        Affiliate::$affiliateType[1]),
                    $affiliate->id,
                    ActivityTypeConstant::CONTENT_DESTROYED,
                    $ipAddress)
            );

            $affiliate->delete();
        } catch (\Exception $exception) {
            Log::error('Failed to delete affiliate content.', [
                'error' => $exception->getMessage(),
                'affiliate_id' => $id,
                'ip_address' => $ipAddress
            ]);
            throw $exception;
        }
    }
}
