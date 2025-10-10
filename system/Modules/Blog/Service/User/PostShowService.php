<?php

namespace Modules\Blog\Service\User;

use Carbon\Carbon;
use Modules\Blog\App\Models\Post;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class PostShowService
{
    function show(string $slug)
    {
        $post = Post::select('id', 'title', 'read_time', 'description', 'created_at')
            ->where('slug', $slug)
            ->first();

        if (!$post) {
            throw new Exception('Post not found.', ErrorCode::NOT_FOUND);
        }

        $mediaFiles = $this->getMediaFiles($post);
        $metaData = $this->getMetaData($post);
        $navigation = $this->getNavigation($post);

        return [
            'title' => $post->title,
            'readTime' => $post->read_time ?? '',
            'description' => $post->description,
            'createdAt' => Carbon::parse($post->created_at)->isoFormat('Do MMMM, YYYY'),
            'files' => $mediaFiles,
            'meta' => $metaData,
            'navigation' => $navigation
        ];
    }

    private function getMediaFiles(Post $post)
    {
        $mediaFiles = [
            'desktop' => null,
            'mobile' => null,
        ];

        $desktopImageFile = $post->filterFiles('desktopImage')->first();
        $mobileImageFile = $post->filterFiles('mobileImage')->first();

        if ($desktopImageFile) {
            $mediaFiles['desktop'] = [
                'id' => $desktopImageFile->id,
                'desktopUrl' => $desktopImageFile->path . '/' . $desktopImageFile->temp_filename,
            ];
        }

        if ($mobileImageFile) {
            $mediaFiles['mobile'] = [
                'id' => $mobileImageFile->id,
                'mobileUrl' => $mobileImageFile->path . '/' . $mobileImageFile->temp_filename,
            ];
        }

        return $mediaFiles;
    }

    private function getMetaData(Post $post)
    {
        $postMetaData = $post->meta()->first();

        return [
            'metaTitle' => $postMetaData['meta_title'],
            'keywords' => json_decode($postMetaData['meta_keywords']),
            'metaDescription' => $postMetaData['meta_description'],
        ];
    }

    private function getNavigation(Post $post)
    {
        $previous = Post::select('title', 'slug')
            ->where('id', '<', $post->id)
            ->orderBy('id', 'desc')
            ->first();

        $next = Post::select('title', 'slug')
            ->where('id', '>', $post->id)
            ->orderBy('id', 'asc')
            ->first();

        return [
            'previous' => $previous ? [
                'title' => $previous->title,
                'slug' => $previous->slug,
            ] : null,
            'next' => $next ? [
                'title' => $next->title,
                'slug' => $next->slug,
            ] : null,
        ];
    }
}
