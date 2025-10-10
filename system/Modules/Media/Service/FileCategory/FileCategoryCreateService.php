<?php

namespace Modules\Media\Service\FileCategory;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Modules\Media\App\Models\FileCategory;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class FileCategoryCreateService
{
    function create($data, $ipAddress)
    {
        FileCategory::create([
            'name' => $data['name'],
            'slug' => $data['url']
        ]);

        Event::dispatch(new AdminUserActivityLogEvent(
                "{$data['name']} file category has been created by: " . Auth::user()->name,
                Auth::id(),
                ActivityTypeConstant::FILE_CATEGORY_CREATED,
                $ipAddress)
        );
    }
}
