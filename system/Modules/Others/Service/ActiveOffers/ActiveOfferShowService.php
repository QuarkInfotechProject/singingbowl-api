<?php

namespace Modules\Others\Service\ActiveOffers;

use Modules\Others\App\Models\ActiveOffer;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ActiveOfferShowService
{
    function show(int $id)
    {
        $feature = ActiveOffer::select('id', 'text', 'is_active')
            ->find($id);

        if (!$feature) {
            throw new Exception('Active offer not found.', ErrorCode::NOT_FOUND);
        }

        $files = $this->getMediaFiles($feature);

        $feature->makeHidden(['id']);

        return [
            'text' => $feature->text,
            'files' => $files,
            'isActive' => $feature->is_active,
        ];
    }

    private function getMediaFiles($feature)
    {
        $mediaFiles = [
            'icon' => '',
        ];

        $image = $feature->filterFiles('image')->first();
        if ($image) {
            $mediaFiles['image'] = [
                'id' => $image->id,
                'imageUrl' => $image->path . '/' . $image->temp_filename,
            ];
        }

        return $mediaFiles;
    }
}
