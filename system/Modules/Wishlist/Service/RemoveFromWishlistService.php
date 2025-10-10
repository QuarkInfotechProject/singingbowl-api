<?php

namespace Modules\Wishlist\Service;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Product\App\Models\Product;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class RemoveFromWishlistService
{
    function removeFromWishlist(string $uuid, $userAgent)
    {
        $user = Auth::user();
        $product = Product::where('uuid', $uuid)
                    ->first();

        if (!$product) {
            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
        }

        $wishlist = $user->wishlist;

        try {
            DB::beginTransaction();

            if ($wishlist && $wishlist->products->contains($product)) {
                $wishlist->products()->detach($product);
            } else {
                throw new Exception('Product not found in wishlist.', ErrorCode::NOT_FOUND);
            }

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error removing product from wishlist: ' . $exception->getMessage(), [
                'exception' => $exception,
                'uuid' => $uuid,
                'user_agent' => $userAgent
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
