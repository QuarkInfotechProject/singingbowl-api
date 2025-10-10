<?php

namespace Modules\Content\Service\Admin\FlashOffer;

use Modules\Content\App\Models\FlashOffer;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class FlashOfferShowService
{
    function show(int $id)
    {
        $content = FlashOffer::select('id', 'name', 'link', 'is_active')
            ->find($id);

        if (!$content) {
            throw new Exception('Content not found.', ErrorCode::NOT_FOUND);
        }

        $mediaFiles = [
            'desktop' => null,
            'mobile' => null,
        ];

        $desktopFile = $content->filterFiles('desktopFile')->first();
        if ($desktopFile) {
            $mediaFiles['desktop'] = [
                'id' => $desktopFile->id,
                'desktopUrl' => $desktopFile->path . '/' . $desktopFile->temp_filename,
            ];
        }

        $mobileFile = $content->filterFiles('mobileFile')->first();
        if ($mobileFile) {
            $mediaFiles['mobile'] = [
                'id' => $mobileFile->id,
                'mobileUrl' => $mobileFile->path . '/' . $mobileFile->temp_filename,
            ];
        }

        return [
            'name' => $content->name,
            'link' => $content->link,
            'isActive' => $content->is_active,
            'files' => $mediaFiles,
        ];
    }
}
