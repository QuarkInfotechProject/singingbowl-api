<?php

namespace Modules\Notification\Service;

use Carbon\Carbon;

class NotificationIndexService
{
    function index()
    {
        $notifications = auth()->user()->notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'productId' => $notification['data']['product_id'],
                'message' => $notification['data']['message'],
                'createdAt' => $this->formatDate($notification->created_at),
                'read' => $notification->read_at ? 'Yes' : 'No'
            ];
        });

        $unReadNotificationCount = auth()->user()->unreadNotifications->count();

        return [
            'unReadNotificationCount' => $unReadNotificationCount,
            'notifications' => $notifications->all()
        ];
    }

    private function formatDate($date): string
    {
        $date = Carbon::parse($date);

        if ($date->isToday()) {
            return $date->diffForHumans();
        } else {
            return $date->isoFormat('D MMMM YYYY');
        }
    }
}
