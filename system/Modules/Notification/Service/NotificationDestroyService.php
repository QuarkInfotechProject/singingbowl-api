<?php

namespace Modules\Notification\Service;

use Illuminate\Support\Facades\Log;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class NotificationDestroyService
{
    function destroy(string $id)
    {
        try {
            $user = auth()->user();

            $notification = $user->notifications()->find($id);

            if (!$notification) {
                throw new Exception('Notification not found.', ErrorCode::NOT_FOUND);
            }

            $notification->delete();
        } catch (\Exception $exception) {
            Log::error('Error deleting notification: ' . $exception->getMessage(), [
                'exception' => $exception,
                'id' => $id,
            ]);
            throw $exception;
        }
    }
}
