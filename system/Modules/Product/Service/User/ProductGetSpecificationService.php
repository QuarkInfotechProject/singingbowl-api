<?php

namespace Modules\Product\Service\User;

use Carbon\Carbon;
use Modules\Attribute\App\Models\AttributeValue;
use Modules\Attribute\App\Models\Attribute;
use Modules\Product\Trait\ValidateProductTrait;

class ProductGetSpecificationService
{
    use ValidateProductTrait;

    function show(string $url)
    {
        $product = $this->validateProduct($url);

        $productData = [];

        $attributes = $this->getAttributesData($product);

        $productReviews = $this->getProductReviews($product);

        $productQuestions = $this->getProductQuestions($product);

        $productData[] = $this->formatProductData($productReviews,  $attributes, $productQuestions);

        return $productData;
    }

    private function getProductQuestions($product)
    {
        $questions = $product->reviews()
            ->with('replies')
            ->with('user')
            ->where('type', 'question')
            ->where('is_approved', true)
            ->get();

        $questionData = [];

        foreach ($questions as $question) {
            $reviewer = $question->name;
            $profilePicture = $question->user ? $question->user->profile_picture : null;
            $questionContent = $question->comment;
            $questionedAt = Carbon::parse($question->created_at)->format('F jS, Y');
            $questionReply = $question->replies->content ?? null;
            $repliedAt = $questionReply ? Carbon::parse($question->replies->created_at)->format('F jS, Y') : null;

            $questionData[] = [
                'inquirer' => $reviewer,
                'profilePicture' => $profilePicture,
                'question' => $questionContent,
                'questionedAt' => $questionedAt,
                'reply' => $questionReply,
                'repliedAt' => $repliedAt
            ];
        }

        return $questionData;
    }

    /**
     * Transforms product attributes into a hierarchical structure grouped by attribute sets
     *
     * @param \Modules\Product\App\Models\Product $product
     * @return array
     */
    private function getAttributesData($product)
    {
        $attributes = $product->attributes;
        $attributeSetGroups = [];

        foreach ($attributes as $attribute) {
            $id = $attribute->id;
            $attributeId = $attribute->attribute_id;

            $attributeModel = Attribute::with('attributeSet')->find($attributeId);

            if (!$attributeModel) {
                continue;
            }

            $attributeName = $attributeModel->name;
            $setName = $attributeModel->attributeSet ? $attributeModel->attributeSet->name : 'Default';

            if (!isset($attributeSetGroups[$setName])) {
                $attributeSetGroups[$setName] = [
                    'name' => $setName,
                    'attributes' => []
                ];
            }

            $values = [];
            foreach ($attribute->values as $value) {
                $attributeValue =AttributeValue::find($value->attribute_value_id);

                if ($attributeValue) {
                    $values[] = $attributeValue->value;
                }
            }

            $attributeExists = false;
            foreach ($attributeSetGroups[$setName]['attributes'] as &$existingAttribute) {
                if ($existingAttribute['key'] === $attributeName) {
                    $attributeExists = true;
                    $existingAttribute['values'] = array_values(array_unique(array_merge($existingAttribute['values'], $values)));
                    break;
                }
            }

            if (!$attributeExists) {
                $attributeSetGroups[$setName]['attributes'][] = [
                    'key' => $attributeName,
                    'values' => $values
                ];
            }
        }

        return array_values($attributeSetGroups);
    }

    private function getProductReviews($product)
    {
        $reviews = $product->reviews()
            ->with('user')
            ->where('type', 'review')
            ->where('is_approved', true)
            ->get();

        $reviewData = [];
        $totalRating = 0;
        $ratingCounts = [];

        foreach ($reviews as $review) {
            $reviewer = $review->user->full_name;
            $profilePicture = $review->user->profile_picture;
            $reviewedAt = Carbon::parse($review->created_at)->format('F jS, Y');
            $reviewCount = $review->rating;
            $reviewComment = $review->comment;
            $reviewReply = $review->replies->content ?? null;
            $repliedAt = $reviewReply ? Carbon::parse($review->replies->created_at)->format('F jS, Y') : null;

            $totalRating += $reviewCount;

            if (!isset($ratingCounts[$reviewCount])) {
                $ratingCounts[$reviewCount] = 0;
            }
            $ratingCounts[$reviewCount]++;

            $reviewImages = $review->images ? array_map(function ($reviewImage) {
                return url('/modules/review/' . $reviewImage['image']);
            }, json_decode($review->images, true)) : [];

            $reviewData[] = [
                'reviewer' => $reviewer,
                'profilePicture' => $profilePicture,
                'reviewedAt' => $reviewedAt,
                'rating' => $reviewCount,
                'comment' => $reviewComment,
                'reply' => $reviewReply,
                'repliedAt' => $repliedAt,
                'images' => $reviewImages
            ];
        }

        $ratingPercentages = [];
        $totalReviews = array_sum($ratingCounts);

        for ($i = 5; $i >= 1; $i--) {
            $count = $ratingCounts[$i] ?? 0;
            $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
            $ratingPercentages[$i] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        $averageRating = round($totalReviews > 0 ? $totalRating / $totalReviews : 0, 1);

        return [
            'reviews' => $reviewData,
            'totalReviews' => $totalReviews,
            'averageRating' => $averageRating,
            'ratingPercentages' => $ratingPercentages,
        ];
    }

    private function formatProductData($productReviews, $attributes, $questionData)
    {
        return [
            'attributes' => $attributes,
            'reviews' => $productReviews,
            'questions' => $questionData,
        ];
    }
}
