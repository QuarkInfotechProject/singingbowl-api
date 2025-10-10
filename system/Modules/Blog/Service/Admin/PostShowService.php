<?php

namespace Modules\Blog\Service\Admin;

use Modules\Blog\App\Models\Post;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class PostShowService
{
    function show(int $id)
    {
        $post = Post::select('id', 'title', 'slug', 'read_time', 'description', 'is_active')
            ->find($id);

        if (!$post) {
            throw new Exception('Post not found.', ErrorCode::NOT_FOUND);
        }

        $mediaFiles = $this->getMediaFiles($post);
        $metaData = $this->getMetaData($post);

        return [
            'title' => $post->title,
            'slug' => $post->slug,
            'readTime' => $post->read_time ?? '',
            'description' => $post->description,
            'isActive' => $post->is_active,
            'files' => $mediaFiles,
            'meta' => $metaData,
        ];
    }

    private function getMediaFiles(Post $post): ?array
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
}
