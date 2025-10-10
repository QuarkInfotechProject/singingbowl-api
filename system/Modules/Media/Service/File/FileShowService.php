<?php

namespace Modules\Media\Service\File;

use Modules\Media\App\Models\File;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class FileShowService
{
    function show(int $id)
    {
        $file = File::with('FileCategory')
            ->select('filename', 'temp_filename', 'alternative_text', 'title', 'caption', 'description', 'path', 'extension', 'file_category_id', 'size', 'height', 'width', 'created_at as createdAt')
            ->find($id);

        if (!$file) {
            throw new Exception('File not found.', ErrorCode::NOT_FOUND);
        }

        $fileCategoryName = $file->fileCategory ? $file->fileCategory->name : 'Ungrouped';

        return [
            'file' => $file->filename . '.' .$file->extension,
            'filename' => $file->filename,
            'fileCategoryId' => optional($file->fileCategory)->id,
            'fileCategoryName' => $fileCategoryName,
            'alternativeText' => $file->alternative_text ?? '',
            'title' => $file->title ?? '',
            'caption' => $file->caption ?? '',
            'description' => $file->description ?? '',
            'size' => $this->formatFileSize($file->size),
            'width' => $file->width,
            'height' => $file->height,
            'url' => $file->path  . '/' . $file->temp_filename,
            'thumbnailUrl' => $file->path  . '/Thumbnail/' . $file->temp_filename,
            'createdAt' => $file->createdAt,
        ];
    }

    private function formatFileSize($sizeInBytes)
    {
        if ($sizeInBytes >= 1024 * 1024) {
            return round($sizeInBytes / (1024 * 1024), 2) . ' MB';
        } elseif ($sizeInBytes >= 1024) {
            return round($sizeInBytes / 1024, 2) . ' KB';
        } else {
            return $sizeInBytes . ' bytes';
        }
    }
}
