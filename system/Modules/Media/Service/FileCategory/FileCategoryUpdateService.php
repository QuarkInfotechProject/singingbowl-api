<?php

namespace Modules\Media\Service\FileCategory;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Modules\Media\App\Models\FileCategory;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class FileCategoryUpdateService
{
    function update($data, $ipAddress)
    {
        $fileCategory = FileCategory::where('slug', $data['url'])->first();

        if (!$fileCategory) {
            throw new Exception('File category not found.', ErrorCode::NOT_FOUND);
        }

        Event::dispatch(new AdminUserActivityLogEvent(
                "{$fileCategory->name} file category has been updated to {$data['name']} by: " . Auth::user()->name,
                Auth::id(),
                ActivityTypeConstant::FILE_CATEGORY_UPDATED,
                $ipAddress)
        );

        $fileCategory->update([
            'name' => $data['name']
        ]);
    }
}
