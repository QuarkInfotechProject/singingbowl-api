<?php

namespace Modules\Blog\Service\User;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Modules\Blog\App\Models\Post;

class PostIndexService
{
    function index(string $keyword = null)
    {
        if (isset($keyword)) {
            $page = 1;
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        $posts = Post::with([
            'files' => function ($q) {
                $q->whereIn('zone', ['desktopImage', 'mobileImage'])
                    ->select('zone', DB::raw("CONCAT(path, '/', temp_filename) AS \"imageUrl\""));
            },
        ])
            ->select('id', 'title', 'slug', 'description', 'created_at as createdAt')
            ->where('is_active', true)
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'LIKE', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(10);

        $posts->transform(function ($post) {
            $post->createdAt = Carbon::parse($post->createdAt)->isoFormat('Do MMMM, YYYY');
            return $post;
        });

        return $posts;
    }
}
