<?php
namespace Modules\Others\Service\CategoriesTrending\Admin;

use Illuminate\Support\Facades\DB;
use Modules\Others\App\Events\CategoriesTrendingUpdated;
use Modules\Others\App\Models\CategoriesTrending;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CategoriesTrendingStatusService
{
    function updateStatus($data)
    {
        $trendingCategory = CategoriesTrending::where('id', $data['id'])->first();

        if (!$trendingCategory) {
            throw new Exception('Trending category not found.', ErrorCode::NOT_FOUND);
        }

        // If trying to activate an inactive trending category
        if ($data['status'] && !$trendingCategory->is_active) {
            $activeCount = CategoriesTrending::where('is_active', true)->count();

            // If already 6 active trending categories, throw exception
            if ($activeCount >= 6) {
                throw new Exception('Cannot activate trending category. Maximum 6 trending categories can be active at a time.', ErrorCode::BAD_REQUEST);
            }
        }

        try {
            DB::beginTransaction();

            $trendingCategory->update([
                'is_active' => $data['status'],
            ]);

            DB::commit();
            
            // Fire event for cache invalidation
            event(new CategoriesTrendingUpdated($trendingCategory->fresh()));

        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
