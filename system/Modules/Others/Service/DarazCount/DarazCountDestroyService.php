<?php

namespace Modules\Others\Service\DarazCount;

use Illuminate\Support\Facades\DB;
use Modules\Others\App\Models\DarazAnalytics;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class DarazCountDestroyService
{
    function destroy(int $id)
    {
        $count = DarazAnalytics::find($id);

        if (!$count) {
            throw new Exception('Daraz count not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $count->delete();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new $exception;
        }
    }
}
