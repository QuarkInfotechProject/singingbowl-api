<?php

namespace Modules\Others\Service\ActiveOffers;

use Illuminate\Support\Facades\DB;
use Modules\Others\App\Models\ActiveOffer;
use Modules\Others\App\Models\Features;

class ActiveOfferCreateService
{
    function create($data)
    {
        try {
            DB::beginTransaction();

            ActiveOffer::create([
                'text' => $data['text'],
                'is_active' => $data['isActive'],
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
