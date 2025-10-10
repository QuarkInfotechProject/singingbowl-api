<?php

namespace Modules\Warranty\Service\WarrantyRegistration;

use Carbon\Carbon;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Warranty\App\Models\WarrantyRegistration;

class WarrantyRegistrationShowService
{
    function show(int $id)
    {
        $warrantyRegistration = WarrantyRegistration::select(
            'name',
            'email',
            'phone',
            'product_name as product',
            'quantity',
            'date_of_purchase as dateOfPurchase',
            'purchased_from as purchasedFrom',
            'order_id as orderId',
            'created_at',
            'address',
            'country_name as countryName',
            'province_name as provinceName',
            'city_name as cityName',
            'zone_name as zoneName',
        )->find($id);

        if (!$warrantyRegistration) {
            throw new Exception('Warranty registration not found.', ErrorCode::NOT_FOUND);
        }

        $warrantyRegistration->submittedAt = Carbon::parse($warrantyRegistration->created_at)
            ->format('jS F,Y @ h:i A');

        $warrantyRegistration->makeHidden(['created_at']);

        return $warrantyRegistration;
    }
}
