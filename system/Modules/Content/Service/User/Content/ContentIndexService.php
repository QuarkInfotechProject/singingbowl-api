<?php

namespace Modules\Content\Service\User\Content;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Content\App\Models\Content;

class ContentIndexService
{
    private string $cacheKey = 'content_index_';
    private int $cacheTTL;

    public function __construct()
    {
        $this->cacheTTL = config('cache_settings.content_index_ttl');
    }

    function index(int $type)
    {
        return Cache::remember($this->cacheKey . $type, $this->cacheTTL, function () use ($type) {
            $contents = Content::with([
                'files' => function ($q) {
                    $q->whereIn('zone', ['desktopImage', 'mobileImage'])
                        ->select(
                            'files.id as file_id',
                            'model_files.model_id as content_id',
                            'model_files.zone',
                            DB::raw("CONCAT(path, '/', temp_filename) AS imageUrl")
                        );
                },
            ])
                ->select('id', 'link')
                ->where('type', $type)
                ->where('is_active', true)
                ->latest()
                ->get();

            return $contents->map(function ($content) {
                $result = [
                    'id' => $content->id,
                    'link' => $content->link,
                ];

                foreach ($content->files as $file) {
                    $result[$file->zone] = $file->imageUrl;
                }

                return $result;
            });
        });
    }
}
