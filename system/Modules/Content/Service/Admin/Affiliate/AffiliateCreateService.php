<?php

namespace Modules\Content\Service\Admin\Affiliate;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Affiliate;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class AffiliateCreateService
{
    function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $affiliate = Affiliate::create([
                'is_partner' => $data['isPartner'],
                'title' => $data['title'],
                'link' => $data['link'],
                'description' => $data['description']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create affiliate.', [
                'error' => $exception->getMessage(),
                'data' => $data,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Affiliate added of type: ' . ($data['isPartner'] ?
                    Affiliate::$affiliateType[0] :
                    Affiliate::$affiliateType[1]),
                $affiliate->id,
                ActivityTypeConstant::CONTENT_CREATED,
                $ipAddress)
        );
    }
}
