<?php

namespace Modules\AdminUser\App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Modules\AdminUser\Service\Auth\AdminUserLogoutService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AdminUserLogoutController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/logout",
     *     summary="Logout Admin User",
     *     tags={"Authentication"},
     *     @OA\Response(
     *     response="200",
     *     description="Admin has been logged out successfully."
     *      ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="UNAUTHORIZED"),
     *             @OA\Property(property="message", type="string", example="Admin user not authenticated.")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AdminUserLogoutService $adminLogoutService)
    {
    }

    function __invoke(Request $request)
    {
        $this->adminLogoutService->logout($request);

        return $this->successResponse('Admin has been logged out successfully.');
    }
}
