<?php

namespace Modules\Notification\App\Http\Controllers;

use Modules\Notification\Service\NotificationIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class NotificationIndexController extends AdminBaseController
{
    public function __construct(private NotificationIndexService $notificationIndexService)
    {
    }

    public function __invoke()
    {
        $notifications = $this->notificationIndexService->index();

//        dd($notifications->data['message']);

        return $this->successResponse('Notifications has been fetched successfully.', $notifications);
    }
}
