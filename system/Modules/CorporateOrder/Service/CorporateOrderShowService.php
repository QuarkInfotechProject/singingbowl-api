<?php

namespace Modules\CorporateOrder\Service;

use Modules\CorporateOrder\App\Models\CorporateOrder;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CorporateOrderShowService
{
    function show(int $id)
    {
        $corporateOrder = CorporateOrder::select('first_name', 'last_name', 'company_name', 'email', 'phone', 'quantity', 'requirement', 'status')
            ->find($id);

        if (!$corporateOrder) {
            throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
        }

        return [
            'firstName' => $corporateOrder->first_name,
            'lastName' => $corporateOrder->last_name,
            'companyName' => $corporateOrder->company_name,
            'email' => $corporateOrder->email,
            'phone' => $corporateOrder->phone,
            'quantity' => $corporateOrder->quantity,
            'requirement' => $corporateOrder->requirement,
            'status' => CorporateOrder::$corporateOrderStatusMapping[$corporateOrder->status],
        ];
    }
}
