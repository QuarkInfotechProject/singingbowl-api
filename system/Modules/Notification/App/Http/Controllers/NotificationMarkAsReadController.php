<?php

namespace Modules\Notification\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Notification\Service\NotificationMarkAsReadService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class NotificationMarkAsReadController extends AdminBaseController
{
    public function __construct(private NotificationMarkAsReadService $notificationMarkAsReadService)
    {
    }

    public function __invoke(Request $request)
    {
        $this->notificationMarkAsReadService->markAsRead($request->get('id'));

        return $this->successResponse('Notification marked as read.');
    }
}
