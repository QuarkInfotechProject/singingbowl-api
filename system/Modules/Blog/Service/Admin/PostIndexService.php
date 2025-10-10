<?php

namespace Modules\Blog\Service\Admin;

use Illuminate\Pagination\Paginator;
use Modules\Blog\App\Models\Post;

class PostIndexService
{
    function index($data)
    {
        if (isset($data['isActive']) && isset($data['title'])) {
            $page = 1;
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        $query = Post::query();

        $query->when(isset($data['isActive']), function ($query) use ($data) {
            return $query->where('is_active', $data['isActive']);
        });

        $query->when(isset($data['title']), function ($query) use ($data) {
            return $query->where('title', 'like', '%' . $data['title'] . '%');
        });

        return $query->select('id', 'title', 'is_active as isActive')
            ->latest()
            ->paginate(20);
    }
}
