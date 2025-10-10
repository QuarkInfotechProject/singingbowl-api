<?php

namespace Modules\Content\Service\Admin\Affiliate;

use Modules\Content\App\Models\Affiliate;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AffiliateShowService
{
    function show(int $id)
    {
        $affiliate = Affiliate::select('id', 'title', 'description', 'link')
            ->find($id);

        if (!$affiliate) {
            throw new Exception('Affiliate content not found.', ErrorCode::NOT_FOUND);
        }

        $files = $this->getMediaFiles($affiliate);

        $affiliate->makeHidden(['id']);

        return [
            'title' => $affiliate->title ?? '',
            'description' => $affiliate->description ?? '',
            'link' => $affiliate->link ?? '',
            'files' => $files
        ];
    }

    private function getMediaFiles($affiliate)
    {
        $mediaFiles = [
            'desktopLogo' => '',
            'mobileLogo' => '',
        ];

        // Get the desktop logo file
        $desktopLogo = $affiliate->filterFiles('desktopLogo')->first();
        if ($desktopLogo) {
            $mediaFiles['desktopLogo'] = [
                'id' => $desktopLogo->id,
                'desktopLogoUrl' => $desktopLogo->path . '/' . $desktopLogo->temp_filename,
            ];
        }

        // Get the mobile logo file
        $mobileLogo = $affiliate->filterFiles('mobileLogo')->first();
        if ($mobileLogo) {
            $mediaFiles['mobileLogo'] = [
                'id' => $mobileLogo->id,
                'mobileLogoUrl' => $mobileLogo->path . '/' . $mobileLogo->temp_filename,
            ];
        }

        return $mediaFiles;
    }
}
