<?php

namespace Modules\Content\Trait;

trait GetMediaFilesTrait
{
    protected function getMediaFiles($content)
    {
        $mediaFiles = [
            'desktopImage' => '',
            'mobileImage' => '',
        ];

        // Get the desktop image file
        $desktopImageFile = $content->filterFiles('desktopImage')->first();
        if ($desktopImageFile) {
            $mediaFiles['desktopImage'] = [
                'id' => $desktopImageFile->id,
                'desktopImageUrl' => $desktopImageFile->path . '/' . $desktopImageFile->temp_filename,
            ];
        }

        // Get the mobile image file
        $mobileImageFile = $content->filterFiles('mobileImage')->first();
        if ($mobileImageFile) {
            $mediaFiles['mobileImage'] = [
                'id' => $mobileImageFile->id,
                'mobileImageUrl' => $mobileImageFile->path . '/' . $mobileImageFile->temp_filename,
            ];
        }

        return $mediaFiles;
    }
}
