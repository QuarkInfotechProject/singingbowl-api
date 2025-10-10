<?php

namespace Modules\Review\Service\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Review\App\Models\Review;

class ReviewIndexService
{
    function index($data)
    {
        $query = Review::query()
            ->select(
            'reviews.uuid',
            'reviews.type',
            'reviews.name',
            'reviews.email',
            'reviews.rating',
            'reviews.comment',
            'reviews.is_approved',
            'reviews.is_replied',
            'products.product_name as productName',
            'products.slug as url',
            'products.uuid as productId',
            'reviews.created_at as reviewedAt',
            DB::raw('COUNT(products.id) OVER() as productReviewCount'),
            DB::raw('CASE WHEN reviews.type = "review" AND reviews.user_id IS NOT NULL THEN users.profile_picture ELSE NULL END as profilePicture')
        )
            ->join('products', 'products.id', '=', 'reviews.product_id')
            ->leftJoin('users', 'users.id', '=', 'reviews.user_id')
            ->latest('reviews.created_at');

        if (!empty($data['type'])) {
            $query->where('reviews.type', $data['type']);
        }

        if (!empty($data['productName'])) {
            $query->where('products.product_name', 'LIKE', '%' . $data['productName'] . '%');
        }

        if (!empty($data['rating'])) {
            $query->where('reviews.rating', $data['rating']);
        }

        if (isset($data['isApproved'])) {
            $query->where('reviews.is_approved', $data['isApproved']);
        }

        $reviews = $query->paginate(25);

        $reviews->getCollection()->transform(function ($review) {
            return [
                'id' => $review->uuid,
                'type' => $review->type,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'isApproved' => $review->is_approved,
                'isReplied' => $review->is_replied,
                'name' => $review->name,
                'email' => $review->email,
                'productName' => $review->productName,
                'url' => $review->url,
                'productId' => $review->productId,
                'productReviewCount' => $review->productReviewCount,
                'profilePicture' => $review->profilePicture,
                'reviewedAt' => Carbon::parse($review->reviewedAt)->format('jS F Y, h:i A')
            ];
        });

        return $reviews;
    }
}
