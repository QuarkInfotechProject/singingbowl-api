<?php

namespace Modules\Others\Service\ActiveOffers;

use Illuminate\Support\Facades\DB;
use Modules\Others\App\Models\ActiveOffer;
use Modules\Others\App\Models\Features;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ActiveOfferDestroyService
{
    function destroy(int $id)
    {
        $feature = ActiveOffer::find($id);

        if (!$feature) {
            throw new Exception('Active offer not found.', ErrorCode::NOT_FOUND);
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
