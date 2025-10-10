<?php

namespace Modules\AdminUser\App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Modules\AdminUser\Service\Auth\AdminUserLoginService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AdminUserLoginController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/login",
     *     tags={"Authentication"},
     *     summary="Authenticate user and set HTTP-only cookie",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Admin user's email",
     *                     example="admin@squarebx.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="Admin user's password",
     *                     example="Password9860!"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Admin has been logged in successfully.",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Email & Password do not match our records."
     *     )
     * )
     */
    function __construct(private AdminUserLoginService $adminUserLoginService)
    {
    }

    function __invoke(Request $request)
    {
        $token = $this->adminUserLoginService->login($request);

        return $this->successResponse('Admin has been logged in successfully.', $token);
    }
}
