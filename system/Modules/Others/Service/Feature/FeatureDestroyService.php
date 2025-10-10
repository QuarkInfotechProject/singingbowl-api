<?php

namespace Modules\Others\Service\Feature;

use Illuminate\Support\Facades\DB;
use Modules\Others\App\Models\Features;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class FeatureDestroyService
{
    function destroy(int $id)
    {
        $feature = Features::find($id);

        if (!$feature) {
            throw new Exception('Feature not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $feature->delete();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new $exception;
        }
    }
}
