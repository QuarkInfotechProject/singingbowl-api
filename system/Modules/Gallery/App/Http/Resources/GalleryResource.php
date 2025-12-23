<?php

namespace Modules\Gallery\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => (bool) $this->status,
            'images' => $this->formatImages(),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }

    private function formatImages(): array
    {
        $images = [];

        foreach ($this->filterFiles('galleryImage')->get() as $file) {
            $images[] = [
                'id' => $file->id,
                'url' => $file->path . '/' . $file->temp_filename,
                'thumbnailUrl' => $file->path . '/Thumbnail/' . $file->temp_filename,
                'mime' => $file->mime,
                'size' => $file->size,
                'width' => $file->width,
                'height' => $file->height,
            ];
        }

        return $images;
    }
}

