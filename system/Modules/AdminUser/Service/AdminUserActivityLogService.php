<?php

namespace Modules\AdminUser\Service;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminUserActivityLogService
{
    function index($data)
    {
        try {
            $query = DB::table('admin_user_activity_log'); // Use query builder on the 'admin_activity_logs' table

            // Filtering by userName (if set)
            if (isset($data['userName'])) {
                $query->where('modifierUsername', 'like', '%' . $data['userName'] . '%');
            }

            // Filtering by activityType (if set)
            if (isset($data['activityType'])) {
                $query->where('activityType', $data['activityType']);
            }

            // Filtering by ipAddress (if set)
            if (isset($data['ipAddress'])) {
                $query->where('ipAddress', $data['ipAddress']);
            }

            // Filtering by date range (if both startDate and endDate are set)
            if (isset($data['startDate']) && isset($data['endDate'])) {
                $query->whereBetween('created_at', [$data['startDate'], $data['endDate']]);
            }

            // Applying final filtering by current user and pagination
            $result = $query->where('modifierId', Auth::user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(25);

            return $result ?? collect([]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
