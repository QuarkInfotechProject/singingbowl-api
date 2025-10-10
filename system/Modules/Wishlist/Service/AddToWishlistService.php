<?php

namespace Modules\Wishlist\Service;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Product\App\Models\Product;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Wishlist\App\Models\Wishlist;

class AddToWishlistService
{
    function addToWishlist(string $uuid, $userAgent)
    {
        $user = Auth::user();
        $product = Product::where('uuid', $uuid)
                    ->first();

        if (!$product) {
            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $wishlist = $user->wishlist ?: Wishlist::create([
                'user_id' => $user->id,
                'user_agent' => $userAgent
            ]);

            if (!$wishlist->products->contains($product)) {
                $wishlist->products()->attach($product);
            }

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error adding product to wishlist: ' . $exception->getMessage(), [
                'exception' => $exception,
                'uuid' => $uuid,
                'user_agent' => $userAgent
            ]);
            DB::rollBack();
            throw new $exception;
        }
    }
}
