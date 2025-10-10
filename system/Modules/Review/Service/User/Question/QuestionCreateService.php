<?php

namespace Modules\Review\Service\User\Question;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Product\App\Models\Product;
use Modules\Review\App\Models\Review;

class QuestionCreateService
{
    function create(array $data, string $ipAddress)
    {
        $userId = $this->getUserId();
        $productId = Product::where('uuid', $data['productId'])->value('id');

        try {
            DB::beginTransaction();

            Review::create([
                'uuid' => Str::uuid(),
                'user_id' => $userId,
                'product_id' => $productId,
                'type' => Review::QUESTION,
                'name' => ucwords($data['name']) ?? Auth::user()->name,
                'email' => $data['email'] ?? Auth::user()->email,
                'comment' => $data['comment'],
                'ip_address' => $ipAddress
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error occurred while submitting question.', [
                'exception' => $exception,
                'productId' => $productId,
            ]);
            DB::rollBack();
            throw $exception;
        }
    }

    private function getUserId()
    {
        if (Auth::check()) {
            return Auth::id();
        }

        return null;
    }
}
