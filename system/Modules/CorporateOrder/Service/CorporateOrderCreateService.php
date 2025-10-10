<?php

namespace Modules\CorporateOrder\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\CorporateOrder\App\Models\CorporateOrder;

class CorporateOrderCreateService
{
    function create($data)
    {
        try {
            DB::beginTransaction();

            CorporateOrder::create([
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'company_name' => $data['companyName'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'quantity' => $data['quantity'],
                'requirement' => $data['requirement']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create CorporateOrder: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
