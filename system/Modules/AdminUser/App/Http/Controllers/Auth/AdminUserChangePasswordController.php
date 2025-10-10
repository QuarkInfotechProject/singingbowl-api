<?php

namespace Modules\AdminUser\App\Http\Controllers\Auth;

use Modules\AdminUser\App\Http\Requests\AdminUserChangePasswordRequest;
use Modules\AdminUser\DTO\AdminUserChangePasswordDTO;
use Modules\AdminUser\Service\Auth\AdminUserChangePasswordService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AdminUserChangePasswordController extends AdminBaseController
{
    /**
     * @Route(uri="/change-password", methods={"POST"})
     * @OA\Post(
     *     path="/api/admin/change-password",
     *     tags={"Admin"},
     *     summary="Change admin password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"currentPassword", "newPassword", "confirmPassword"},
     *                 @OA\Property(
     *                     property="currentPassword",
     *                     type="string",
     *                     description="The current password of the admin.",
     *                     example="Password9860!"
     *                 ),
     *                 @OA\Property(
     *                     property="newPassword",
     *                     type="string",
     *                     description="The new password to set.",
     *                     example="Password9860?"
     *                 ),
     *                 @OA\Property(
     *                     property="confirmPassword",
     *                     type="string",
     *                     description="Confirm the new password.",
     *                     example="Password9860?"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Password has been changed successfully",
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request - The request body is invalid.",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AdminUserChangePasswordService $adminUserChangePasswordService)
    {
    }

    function __invoke(AdminUserChangePasswordRequest $request)
    {
        $adminUserChangePasswordDTO = AdminUserChangePasswordDTO::from($request->all());

        $this->adminUserChangePasswordService->changePassword($adminUserChangePasswordDTO, $request->getClientIp());

        return $this->successResponse('Password has been changed successfully.');
    }
}
