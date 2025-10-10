<?php

namespace Modules\Content\Service\Admin\Affiliate;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Affiliate;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AffiliateUpdateService
{
    function update($data, string $ipAddress)
    {
        $affiliate = Affiliate::find($data['id']);

        if (!$affiliate) {
            throw new Exception('Affiliate content not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $affiliate->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'link' => $data['link']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update affiliate content.', [
                'error' => $exception->getMessage(),
                'affiliate_id' => $data['id'],
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Affiliate content updated of type: ' . ($affiliate->is_partner ?
                    Affiliate::$affiliateType[0] :
                    Affiliate::$affiliateType[1]),
                $affiliate->id,
                ActivityTypeConstant::CONTENT_UPDATED,
                $ipAddress)
        );
    }
}
