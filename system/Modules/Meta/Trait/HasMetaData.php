<?php

namespace Modules\Meta\Trait;

use Modules\Meta\App\Models\MetaData;

trait HasMetaData
{
    public function meta()
    {
        return $this->morphOne(MetaData::class, 'entity')->withDefault();
    }

    public function saveMetaData($data = [])
    {
        $this->meta->fill([
            'meta_title' => $data['metaTitle'] ?? null,
            'meta_keywords' => json_encode($data['keywords'] ?? []),
            'meta_description' => $data['metaDescription'] ?? null,
        ])->save();
    }
}
