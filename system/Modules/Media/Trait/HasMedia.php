<?php

namespace Modules\Media\Trait;

use Illuminate\Support\Arr;
use Modules\Media\App\Models\File;

trait HasMedia
{
    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_files')
            ->withPivot(['id', 'zone'])
            ->withTimestamps();
    }

    public function productOptionFiles()
    {
        return $this->morphToMany(File::class, 'model', 'model_files')
            ->withPivot(['id', 'zone'])
            ->withTimestamps();
    }

    public function filterFiles($zone)
    {
        return $this->files()->wherePivot('zone', $zone);
    }

    public function ProductOptionFilterFiles($zone)
    {
        return $this->productOptionFiles()->wherePivot('zone', $zone);
    }

    public function syncFiles($files = [])
    {
        $entityType = get_class($this);

        if ($files) {
            foreach ($files as $zone => $fileIds) {
                $syncList = [];

                foreach (Arr::wrap($fileIds) as $fileId) {
                    if (!empty($fileId) && File::where('id', $fileId)->exists()) {
                        $syncList[$fileId]['zone'] = $zone;
                    }
                }

                $this->filterFiles($zone)->detach();
                $this->filterFiles($zone)->attach($syncList);
            }
        }
    }

    public function syncProductOptionFiles($files = [])
    {
        $entityType = get_class($this);

        if ($files) {
            foreach ($files as $zone => $fileIds) {
                $syncList = [];

                foreach (Arr::wrap($fileIds) as $fileId) {
                    if (!empty($fileId)) {
                        $syncList[$fileId]['zone'] = $zone;
                    }
                }

                $this->ProductOptionFilterFiles($zone)->detach();
                $this->ProductOptionFilterFiles($zone)->attach($syncList);
            }
        }
    }
}
