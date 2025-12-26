<?php

namespace Modules\Review\Service\User\Review;

use Carbon\Carbon;
use Modules\Review\App\Models\Review;

class ReviewIndexService
{
    public function index($perPage = 20)
    {
        $reviews = Review::query()
            ->select(
                'reviews.uuid',
                'reviews.name',
                'reviews.rating',
                'reviews.comment',
                'reviews.created_at',
                'products.product_name',
                'products.slug as product_slug',
                'products.uuid as product_uuid',
                'users.profile_picture'
            )
            ->join('products', 'products.id', '=', 'reviews.product_id')
            ->leftJoin('users', 'users.id', '=', 'reviews.user_id')
            ->where('reviews.type', 'review')
            ->where('reviews.is_approved', true)
            ->latest('reviews.created_at')
            ->paginate($perPage);

        $reviews->getCollection()->transform(function ($review) {
            return [
                'id' => $review->uuid,
                'name' => $review->name,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'profilePicture' => $review->profile_picture,
                'productName' => $review->product_name,
                'productSlug' => $review->product_slug,
                'productId' => $review->product_uuid,
                'reviewedAt' => Carbon::parse($review->created_at)->format('jS F Y'),
                'timeAgo' => Carbon::parse($review->created_at)->diffForHumans(),
            ];
        });

        return $reviews;
    }
}
