<?php

namespace Modules\Review\Service\Admin;

use Carbon\Carbon;
use Modules\Review\App\Models\Review;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ReviewShowService
{
    public function show(string $uuid)
    {
        try {
            $review = Review::with([
                'product:id,uuid,product_name',
                'replies:id,review_id,content',
                'user:id,profile_picture'
            ])
                ->where('uuid', $uuid)
                ->first(['id', 'name', 'email', 'rating', 'type', 'comment', 'is_approved', 'created_at', 'images', 'user_id']); // Include user_id to fetch related user data
        } catch (\Exception $exception) {
            throw new $exception;
        }

        if (!$review) {
            throw new Exception('Product review not found.', ErrorCode::NOT_FOUND);
        }

        $review->createdAt = Carbon::parse($review->created_at)->format('jS F Y, h:i A');

        $image = $review->images ? array_map(function ($reviewImage) {
            return url('/modules/review/' . $reviewImage['image']);
        }, json_decode($review->images, true)) : [];

        if ($review->user) {
            $profilePicture = $review->user->profile_picture;
        }

        return [
            'userName' => $review->name,
            'email' => $review->email,
            'type' => $review->type,
            'rating' => $review->rating,
            'comment' => $review->comment,
            'isApproved' => $review->is_approved,
            'createdAt' => $review->createdAt,
            'image' => $image,
            'reply' => $review->replies->content ?? '',
            'profilePicture' => $profilePicture ?? null,
        ];
    }
}
