<?php

namespace Modules\Cart\App\Http\Controllers;

use Modules\Cart\Service\GetTokenService;
use Modules\Shared\App\Http\Controllers\GuestBaseController;

/**
 * This controller handles the generation of a new guest token.
 * Guest users must call this endpoint first to receive a token
 * which they'll use in all subsequent cart-related operations.
 */
class GetGuestCartTokenController extends GuestBaseController
{
    public function __construct(private GetTokenService $getTokenService)
    {
    }

    /**
     * Generate a new guest token for the user
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke()
    {
        $guestCart = $this->getTokenService->execute();

        return $this->successResponse('Guest token created. Please include this token in the X-Guest-Token header for all subsequent requests.', [
            'guest_token' => $guestCart->guest_token,
            'id' => $guestCart->id
        ]);
    }
}