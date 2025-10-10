<?php

namespace Modules\CorporateOrder\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\CorporateOrder\App\Models\CorporateOrder;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CorporateOrderChangeStatusService
{
    function changeStatus($data)
    {
        $corporateOrder = CorporateOrder::find($data['id']);

        if (!$corporateOrder) {
            throw new Exception('Corporate order not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $corporateOrder->update([
               'status' => $data['status']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update corporate order status: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
