<?php

namespace Modules\Media\Service\FileCategory;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Modules\Media\App\Models\FileCategory;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\ImageUpload\Service\ImageUploadService;
use Modules\Shared\StatusCode\ErrorCode;

class FileCategoryDestroyService
{
    function __construct(private ImageUploadService $imageUploadService)
    {
    }

    function destroy(string $url, $ipAddress)
    {
        $fileCategory = FileCategory::where('slug', $url)->first();

        if (!$fileCategory) {
            throw new Exception('File category not found.', ErrorCode::NOT_FOUND);
        }

        foreach ($fileCategory->files as $file) {
            $this->imageUploadService->remove($file->temp_filename, 'modules/files/');
            $this->imageUploadService->remove($file->temp_filename, 'modules/files/Thumbnail/');

            $file->delete();
        }

        $fileCategory->delete();

        Event::dispatch(new AdminUserActivityLogEvent(
                "{$fileCategory->name} file category has been destroyed by: " . Auth::user()->name,
                Auth::id(),
                ActivityTypeConstant::FILE_CATEGORY_DESTROYED,
                $ipAddress)
        );
    }
}
