<?php

namespace Modules\Cart\Service;

use Modules\Cart\App\Models\GuestCart;
use Illuminate\Support\Str;

class GetTokenService
{
    public function __construct(private CartRepository $cartRepository)
    {
    }

    /**
     * Create a new guest cart and return it with a newly generated token.
     * This should be the first method called by a guest user to obtain a token
     * that will be used in subsequent requests.
     *
     * @return GuestCart
     */
    public function execute(): GuestCart
    {
        $guestToken = (string) Str::uuid();

        return $this->cartRepository->createOrGetGuestCart($guestToken);
    }
}