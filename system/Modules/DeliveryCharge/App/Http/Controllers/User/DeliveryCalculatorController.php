<?php

namespace Modules\DeliveryCharge\App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\DeliveryCharge\Service\User\DeliveryCalculatorService;
use Modules\DeliveryCharge\App\Models\DeliveryCharge; 

class DeliveryCalculatorController extends Controller
{
    protected $calculator;

    public function __construct(DeliveryCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    public function getCalculatedCharge(Request $request)
    {
        $cartData = $request->input('cart_data.data');
        $addressData = $request->input('address_data.data.address');

        if (!$cartData || !$addressData) {
            return response()->json(['success' => false, 'message' => 'Missing cart or address data'], 400);
        }

        // Fetch all rows (so we can filter by country in the service)
        $allCharges = DeliveryCharge::all()->toArray();

        $result = $this->calculator->calculate($cartData, $addressData, $allCharges);

        return response()->json([
            'success' => true,
            'message' => 'Delivery charge calculated.',
            'data' => [
                'delivery_cost' => $result['cost'],
                'delivery_type' => $result['type'],
                'currency' => 'NPR'
            ]
        ]);
    }
}
