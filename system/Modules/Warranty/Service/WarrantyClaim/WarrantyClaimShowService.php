<?php

namespace Modules\Warranty\Service\WarrantyClaim;

use Carbon\Carbon;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Warranty\App\Models\WarrantyClaim;

class WarrantyClaimShowService
{
    function show(int $id)
    {
        $warrantyClaim = WarrantyClaim::select(
            'name',
            'email',
            'phone',
            'product_name',
            'quantity',
            'purchased_from',
            'images',
            'description',
            'created_at',
            'address',
            'country_name',
            'province_name',
            'city_name',
            'zone_name',
        )->find($id);

        if (!$warrantyClaim) {
            throw new Exception('Warranty registration not found.', ErrorCode::NOT_FOUND);
        }

        $images = [];
        if ($warrantyClaim->images) {
            $warrantyClaimImages = json_decode($warrantyClaim->images, true);
            $images = array_map(function ($image) {
                return url('/WarrantyClaimImages/' . $image['image']);
            }, $warrantyClaimImages);
        }

        return [
            'name' => $warrantyClaim->name,
            'email' => $warrantyClaim->email,
            'phone' => $warrantyClaim->phone,
            'product' => $warrantyClaim->product_name,
            'quantity' => $warrantyClaim->quantity,
            'purchasedFrom' => $warrantyClaim->purchased_from,
            'images' => $images,
            'description' => $warrantyClaim->description,
            'address' => $warrantyClaim->address,
            'countryName' => $warrantyClaim->country_name,
            'provinceName' => $warrantyClaim->province_name,
            'cityName' => $warrantyClaim->city_name,
            'zoneName' => $warrantyClaim->zone_name,
            'submittedAt' => Carbon::parse($warrantyClaim->created_at)->format('jS F,Y @ h:i A')
        ];
    }
}
