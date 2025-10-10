<?php

namespace Modules\Content\Service\Admin\BestSeller;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\BestSeller;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class BestSellerDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        try {
            $content = BestSeller::find($id);

            if (!$content) {
                throw new Exception('Best seller content not found.', ErrorCode::NOT_FOUND);
            }

            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'Best seller content destroyed of name: ' . $content->name,
                    $content->id,
                    ActivityTypeConstant::BEST_SELLER_CONTENT_DESTROYED,
                    $ipAddress
                )
            );

            $content->delete();
        } catch (\Exception $exception) {
            Log::error('Failed to destroy best seller content.', [
                'error' => $exception->getMessage(),
                'id' => $id
            ]);
            throw $exception;
        }
    }
}
