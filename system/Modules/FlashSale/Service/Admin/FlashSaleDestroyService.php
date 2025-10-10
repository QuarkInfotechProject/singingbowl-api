<?php

namespace Modules\FlashSale\Service\Admin;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\FlashSale\App\Models\FlashSale;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class FlashSaleDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        try {
            $flashSale = FlashSale::find($id);

            if (!$flashSale) {
                throw new Exception('Flash Sale campaign not found.', ErrorCode::NOT_FOUND);
            }

            Event::dispatch(new AdminUserActivityLogEvent(
                'Flash Sale campaign destroyed with name: "' . $flashSale->campaign_name . '"',
                $flashSale->id,
                ActivityTypeConstant::SALE_DESTROYED,
                $ipAddress
            ));

            $flashSale->delete();
        } catch (\Exception $exception) {
            Log::error('Failed to destroy flash sale.', [
                'error' => $exception->getMessage(),
                'flash_sale_id' => $id,
                'ip_address' => $ipAddress
            ]);
            throw $exception;
        }
    }
}
