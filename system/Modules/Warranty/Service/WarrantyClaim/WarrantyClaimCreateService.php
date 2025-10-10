<?php

namespace Modules\Warranty\Service\WarrantyClaim;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Shared\ImageUpload\Service\TempImageUploadService;
use Modules\Warranty\App\Models\WarrantyClaim;

class WarrantyClaimCreateService
{
    function __construct(private TempImageUploadService $tempImageUploadService)
    {
    }

    function create($data)
    {
        $uploadedImages = [];
        try {
            if (isset($data['images'])) {
                foreach ($data['images'] as $image) {
                    try {
                        $fileName = $this->tempImageUploadService->upload($image, public_path('WarrantyClaimImages'));
                        $uploadedImages[] = [
                            'image' => $fileName,
                        ];
                    } catch (\Exception $uploadException) {
                        Log::error('Error uploading image: ' . $uploadException->getMessage(), [
                            'exception' => $uploadException,
                            'image' => $image
                        ]);
                    }
                }
            }

            DB::beginTransaction();

            $warranty = WarrantyClaim::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'product_name' => $data['productName'],
                'quantity' => $data['quantity'],
                'purchased_from' => $data['purchasedFrom'],
                'images' => json_encode($uploadedImages),
                'description' => $data['description'],
                'address' => $data['address'],
                'country_name' => $data['countryName'] ?? 'NP',
                'province_name' => $data['provinceName'],
                'city_name' => $data['cityName'],
                'zone_name' => $data['zoneName'],
            ]);

            DB::commit();

            return $warranty->product_name;

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error creating warranty claim: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data,
                'uploadedImages' => $uploadedImages
            ]);
            throw $exception;
        }
    }
}
