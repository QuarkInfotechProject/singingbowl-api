<?php

namespace Modules\Notification\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Notification\Service\NotificationDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class NotificationDestroyController extends AdminBaseController
{
    function __construct(private NotificationDestroyService $notificationDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->notificationDestroyService->destroy($request->get('id'));

        return $this->successResponse('Notification has been deleted successfully.');
    }
}
