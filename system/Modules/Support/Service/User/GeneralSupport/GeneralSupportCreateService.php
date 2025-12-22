<?php

namespace Modules\Support\Service\User\GeneralSupport;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Support\App\Models\GeneralSupport;

class GeneralSupportCreateService
{
    function create($data)
    {
        try {
            DB::beginTransaction();

            GeneralSupport::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $data['subject'],
                'phone' => $data['phone'],
                'message' => $data['message'],
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error occurred while creating a GeneralSupport record.', [
                'exception' => $exception,
                'data' => $data,
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
