<?php
namespace Modules\Others\Service\CategoriesTrending\Admin;

use Illuminate\Support\Facades\DB;
use Modules\Others\App\Events\CategoriesTrendingDeleted;
use Modules\Others\App\Models\CategoriesTrending;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CategoriesTrendingDestroyService
{
    public function destroy($id)
    {
        $trendingCategory = CategoriesTrending::find($id);

        if (!$trendingCategory) {
            throw new Exception('Trending category not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $trendingCategoryId = $trendingCategory->id;
            $trendingCategory->delete();

            DB::commit();
            
            // Fire event for cache invalidation
            event(new CategoriesTrendingDeleted($trendingCategoryId));
            
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
