<?php

namespace Modules\Menu\Service;

use Illuminate\Support\Facades\Log;
use Modules\Menu\App\Models\Menu;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class MenuChangeStatusService
{
    function changeStatus(int $id)
    {
        try {
            $menu = Menu::find($id);

            if (!$menu) {
                throw new Exception('Menu not found.', ErrorCode::NOT_FOUND);
            }

            $menu->update(['status' => !$menu->status]);
        } catch (\Exception $exception) {
            Log::error('Failed to change status of menu: ' . $exception->getMessage(), [
                'exception' => $exception,
                'id' => $id
            ]);
            throw $exception;
        }
    }
}
