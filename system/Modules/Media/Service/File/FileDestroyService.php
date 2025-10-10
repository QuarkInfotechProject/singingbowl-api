<?php

namespace Modules\Media\Service\File;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Media\App\Models\File;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\ImageUpload\Service\ImageUploadService;
use Modules\Shared\StatusCode\ErrorCode;

class FileDestroyService
{
    function __construct(private ImageUploadService $imageUploadService)
    {
    }

    function destroy(array $ids, string $ipAddress)
    {
        foreach ($ids as $id) {
            $file = File::find($id);

            if (!$file) {
                Log::warning("File with ID {$id} not found.", ['id' => $id]);
                throw new Exception('File not found.', ErrorCode::NOT_FOUND);
            }

            try {
                $this->imageUploadService->remove($file->temp_filename, 'modules/files/');
                $this->imageUploadService->remove($file->temp_filename, 'modules/files/Thumbnail/');

                Event::dispatch(new AdminUserActivityLogEvent(
                    "{$file->filename} has been destroyed by: " . Auth::user()->name,
                    Auth::id(),
                    ActivityTypeConstant::FILE_DESTROYED,
                    $ipAddress
                ));

                $file->delete();
            } catch (\Exception $exception) {
                Log::error("Failed to destroy file with ID {$id}: " . $exception->getMessage(), [
                    'exception' => $exception,
                    'file_id' => $file->id,
                    'filename' => $file->filename,
                    'ipAddress' => $ipAddress,
                ]);

                throw $exception;
            }
        }
    }
}
