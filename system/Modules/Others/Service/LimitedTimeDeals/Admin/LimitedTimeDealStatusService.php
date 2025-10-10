<?php

namespace Modules\Others\Service\LimitedTimeDeals\Admin;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Others\App\Models\LimitedTimeDeals;

class LimitedTimeDealStatusService
{
    public function toggleStatus($id)
    {
        try {
            $deal = LimitedTimeDeals::findOrFail($id);

            $deal->status = $deal->status == 1 ? 0 : 1;
            $deal->save();

            return [
                'data' => [
                    'id' => $deal->id,
                    'status' => $deal->status,
                    'message' => 'Status changed to ' . ($deal->status == 1 ? 'active' : 'inactive')
                ]
            ];
        } catch (ModelNotFoundException $e) {
            throw new Exception('Limited time deal not found.');
        } catch (Exception $e) {
            throw $e;
        }
    }
}