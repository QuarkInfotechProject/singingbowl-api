<?php

namespace Modules\Shared\App\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;

class AdminUserActivityLogListener
{
    /**
     * Handle the event.
     */
    public function handle(AdminUserActivityLogEvent $event): void
    {
        $activity = $event->adminUserActivityLogDTO;
        $time = Carbon::now()->toDateTimeString();
        DB::table('admin_user_activity_log')->insert([
            'description' => $activity->description,
            'activityType' => $activity->activityType,
            'ipAddress' => $activity->ipAddress,
            'modifierId' => $activity->modifierId,
            'modifierUsername' => $activity->modifierUsername,
            'objectId' => $activity->objectId,
            'created_at' => $time,
            'updated_at' => $time,
        ]);
    }
}
