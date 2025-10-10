<?php

namespace Modules\Content\Service\Admin\BestSeller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\BestSeller;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class BestSellerCreateService
{
    function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $content = BestSeller::create([
                'name' => $data['name'],
                'link' => $data['link'],
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create best seller content.', [
                'error' => $exception->getMessage(),
                'data' => $data
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Best seller content added of name: ' . $content->name,
                $content->id,
                ActivityTypeConstant::BEST_SELLER_CONTENT_CREATED,
                $ipAddress)
        );
    }
}
