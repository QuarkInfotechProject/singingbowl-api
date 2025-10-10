<?php

namespace Modules\Cart\Service;

use Modules\Cart\App\Models\GuestCart;
use Modules\User\Service\MergeCartsService;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\Log;

class GuestCartService
{
    public function __construct(private MergeCartsService $mergeCartsService)
    {
    }

    /**
     * Merge guest cart with user cart when user registers or logs in.
     *
     * @param string $guestToken
     * @param User $user
     * @return void
     */
    public function mergeWithUserCart(string $guestToken, User $user): void
    {
        try {
            $this->mergeCartsService->execute($guestToken, $user);
            Log::info('Guest cart merged successfully.', [
                'guest_token' => $guestToken,
                'user_id' => $user->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to merge guest cart.', [
                'guest_token' => $guestToken,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
