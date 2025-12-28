<?php

namespace Modules\Gallery\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'imageUrl' => $this->path . '/' . $this->temp_filename,
            'thumbnailUrl' => $this->path . '/Thumbnail/' . $this->temp_filename,
            'mime' => $this->mime,
            'size' => $this->size,
            'width' => $this->width,
            'height' => $this->height,
            'status' => null, // Maintained for frontend compatibility as per request
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

