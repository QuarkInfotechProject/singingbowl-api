<?php

namespace Modules\Review\Service\User\Review;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Order\App\Models\OrderItem;
use Modules\Product\App\Models\Product;
use Modules\Review\App\Events\ReviewCreated;
use Modules\Review\App\Models\Review;
use Modules\Shared\Exception\Exception;
use Modules\Shared\ImageUpload\Service\TempImageUploadService;
use Modules\Shared\StatusCode\ErrorCode;

class ReviewCreateService
{
    function __construct(private TempImageUploadService $tempImageUploadService)
    {
    }

    function create(array $data, string $ipAddress)
    {
        $user = Auth::user();
        $orderItem = OrderItem::find($data['orderItemId']);

        if (!$orderItem) {
            Log::error('Order item not found.', [
                'orderItemId' => $data['orderItemId'],
            ]);
            throw new Exception('Item not found.', ErrorCode::NOT_FOUND);
        }

        $product = Product::where('uuid', $data['productId'])->first();

//        $existingReview = Review::where('user_id', $user->id)
//            ->where('product_id', $product->id)
//            ->first();
//
//        if ($existingReview) {
//            Log::error('Review already exists for this product.', [
//                'userId' => $user->id,
//                'productId' => $product->id,
//            ]);
//            throw new Exception('Your review already exists for this product.', ErrorCode::CONFLICT);
//        }

        $uploadedImages = $this->uploadImages($data['images'] ?? []);

        try {
            DB::beginTransaction();

            $review = Review::create([
                'uuid' => Str::uuid(),
                'user_id' => $user->id,
                'product_id' => $product->id,
                'type' => Review::REVIEW,
                'name' => $user->full_name,
                'email' => $user->email,
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'images' => json_encode($uploadedImages),
                'ip_address' => $ipAddress
            ]);

            $orderItem->update(['is_reviewed' => true]);

            DB::commit();
            
            // Fire review created event for cache invalidation
            ReviewCreated::dispatch($review);
        } catch (\Exception $exception) {
            Log::error('Error occurred while creating review.', [
                'exception' => $exception,
                'data' => $data,
                'userId' => $user->id,
                'orderItemId' => $data['orderItemId'],
                'productId' => $product->id,
            ]);
            DB::rollBack();
            throw $exception;
        }
    }

    private function uploadImages(array $images): array
    {
        $uploadedImages = [];
        foreach ($images as $image) {
            $fileName = $this->tempImageUploadService->upload($image, public_path('modules/review'));
            $uploadedImages[] = ['image' => $fileName];
        }
        return $uploadedImages;
    }
}
