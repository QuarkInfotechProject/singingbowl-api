<?php

namespace Modules\Shared\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Modules\Shared\DTO\ActivityLog\AdminUserActivityLogDTO;

class AdminUserActivityLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public AdminUserActivityLogDTO $adminUserActivityLogDTO;

    /**
     * Create a new event instance.
     */
    public function __construct(string $description, ?string $objectId, string $activityType, string $ipAddress)
    {
        $adminUserActivityLog = AdminUserActivityLogDTO::from(
            [
                'description' => $description,
                'objectId' => $objectId,
                'activityType' => $activityType,
                'ipAddress' => $ipAddress,
                'modifierId' => Auth::guard('admin')->user()->getAuthIdentifier(),
                'modifierUsername' => Auth::guard('admin')->user()->name
            ]
        );

        $this->adminUserActivityLogDTO = $adminUserActivityLog;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
