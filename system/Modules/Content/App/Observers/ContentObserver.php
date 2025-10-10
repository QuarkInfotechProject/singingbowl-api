<?php

namespace Modules\Content\App\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Content\App\Models\Content;

class ContentObserver
{
    private function clearCache(Content $content)
    {
        $cacheKey = 'content_index_' . $content->type;
        Cache::forget($cacheKey);
    }

    public function created(Content $content)
    {
        $this->clearCache($content);
    }

    public function updated(Content $content)
    {
        $this->clearCache($content);
    }

    public function deleted(Content $content)
    {
        $this->clearCache($content);
    }
}
