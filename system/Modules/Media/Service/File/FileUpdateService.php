<?php

namespace Modules\Media\Service\File;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Media\App\Models\File;
use Modules\Media\App\Models\FileCategory;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class FileUpdateService
{
    function update($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $file = File::find($data['id']);

            if (!$file) {
                throw new Exception('File not found.', ErrorCode::NOT_FOUND);
            }

            if (isset($data['fileCategoryId'])) {
                $fileCategoryId = FileCategory::find($data['fileCategoryId']);

                if (!$fileCategoryId) {
                    throw new Exception('File category not found.', ErrorCode::NOT_FOUND);
                }

                $file->update([
                    'filename' => $data['fileName'],
                    'alternative_text' => $data['alternativeText'],
                    'title' => $data['title'],
                    'caption' => $data['caption'],
                    'description' => $data['description'],
                    'file_category_id' => $fileCategoryId->id,
                    'is_grouped' => true
                ]);
            } else {
                $file->update([
                    'filename' => $data['fileName'],
                    'alternative_text' => $data['alternativeText'],
                    'title' => $data['title'],
                    'caption' => $data['caption'],
                    'description' => $data['description'],
                ]);
            }

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update file during transaction: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(new AdminUserActivityLogEvent(
            "{$file->filename} has been updated by: " . Auth::user()->name,
            Auth::id(),
            ActivityTypeConstant::FILE_UPDATED,
            $ipAddress
        ));
    }
}
