<?php

namespace Modules\Support\Service\User\OrderSupport;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Shared\ImageUpload\Service\TempImageUploadService;
use Modules\Support\App\Models\OrderSupport;

class OrderSupportCreateService
{
    function __construct(private TempImageUploadService $tempImageUploadService)
    {
    }

    function create($data)
    {
        if (isset($data['image'])) {
            $fileName = $this->tempImageUploadService->upload($data['image'], public_path('OrderSupportImages'));
        }

        try {
            DB::beginTransaction();

            OrderSupport::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'order_id' => $data['orderId'],
                'payment_transaction_id' => $data['paymentTransactionId'],
                'message' => $data['message'],
                'image' => $fileName ?? null,
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error occurred while creating a OrderSupport record.', [
                'exception' => $exception,
                'data' => $data,
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
