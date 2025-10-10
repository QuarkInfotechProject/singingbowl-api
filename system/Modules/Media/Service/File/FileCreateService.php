<?php

namespace Modules\Media\Service\File;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Media\App\Models\File;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\ImageUpload\Service\ImageUploadService;

class FileCreateService
{
    function __construct(private ImageUploadService $imageUploadService)
    {
    }

    function create($request, string $ipAddress)
    {
        $files = $request->file('files');

        try {
            DB::beginTransaction();

            foreach ($files as $file) {
                $this->createFile($file, $request->fileCategoryId, $ipAddress);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Failed to process files during transaction: ' . $exception->getMessage(), [
                'exception' => $exception,
                'fileCategoryId' => $request->fileCategoryId,
                'ipAddress' => $ipAddress
            ]);
            throw $exception;
        }
    }

    private function createFile($file, $fileCategoryId, $ipAddress)
    {
        try {
            $size = $file->getSize();
            $fileName = $this->imageUploadService->upload($file, public_path('modules/files'));
            $filePath = public_path('modules/files') . '/' . $fileName;
            [$width, $height] = getimagesize($filePath);

            $fileData = [
                'file_category_id' => $fileCategoryId,
                'is_grouped' => (bool)$fileCategoryId,
                'filename' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'temp_filename' => $fileName,
                'disk' => config('filesystems.default'),
                'path' => url('modules/files'),
                'extension' => $file->guessClientExtension() ?? '',
                'mime' => $file->getClientMimeType(),
                'size' => $size,
                'width' => $width,
                'height' => $height,
            ];

            File::create($fileData);

            $this->logFileCreatedEvent($fileData['filename'], $ipAddress);
        } catch (\Exception $exception) {
            Log::error('Failed to create file: ' . $exception->getMessage(), [
                'exception' => $exception,
                'file' => $file,
                'fileCategoryId' => $fileCategoryId,
                'ipAddress' => $ipAddress
            ]);
            throw $exception;
        }
    }

    private function logFileCreatedEvent($filename, $ipAddress)
    {
        Event::dispatch(new AdminUserActivityLogEvent(
            "$filename has been created by: " . Auth::user()->name,
            Auth::id(),
            ActivityTypeConstant::FILE_CREATED,
            $ipAddress
        ));
    }
}
