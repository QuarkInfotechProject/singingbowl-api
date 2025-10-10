<?php

namespace Modules\Others\Service\Feature;

use Illuminate\Support\Facades\DB;
use Modules\Others\App\Models\Features;

class FeatureCreateService
{
    function create($data)
    {
        try {
            DB::beginTransaction();

            Features::create([
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
