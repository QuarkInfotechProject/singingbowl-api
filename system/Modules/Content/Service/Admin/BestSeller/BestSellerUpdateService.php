<?php

namespace Modules\Content\Service\Admin\BestSeller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\BestSeller;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class BestSellerUpdateService
{
    function update($data, string $ipAddress)
    {
        $content = BestSeller::find($data['id']);

        if (!$content) {
            throw new Exception('Best seller content not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $content->update([
                'name' => $data['name'],
                'link' => $data['link'],
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update best seller content.', [
                'error' => $exception->getMessage(),
                'data' => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Best seller content updated.',
                $content->id,
                ActivityTypeConstant::BEST_SELLER_CONTENT_UPDATED,
                $ipAddress)
        );
    }
}
