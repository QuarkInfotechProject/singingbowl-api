<?php

namespace Modules\Notification\Service;

use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class NotificationMarkAsReadService
{
    function markAsRead($id = null)
    {
        $user = auth()->user();

        if ($id) {
            $notification = $user->notifications()->find($id);

            if (!$notification) {
                throw new Exception('Notification not found.', ErrorCode::NOT_FOUND);
            }

            $notification->markAsRead();
        } else {
            $user->unreadNotifications->markAsRead();
        }
    }
}
