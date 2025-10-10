<?php

namespace Modules\AdminUser\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\AdminUser\Service\AdminUserActivateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AdminUserActivateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *      path="/api/admin/user/activate",
     *      operationId="activateAdminUser",
     *      tags={"Admin"},
     *      summary="Activate an individual admin user.",
     *      description="Activate an individual admin user based on the provided UUID.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="UUID of the admin user to activate.",
     *          @OA\JsonContent(
     *              @OA\Property(property="uuid", type="string"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Admin user has been activated successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Admin user has been activated successfully."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          ),
     *      ),
     *     @OA\Response(
     *           response=404,
     *           description="User not found.",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="User not found."),
     *           ),
     *       ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AdminUserActivateService $adminUserActivateService)
    {
    }

    function __invoke(Request $request)
    {
        $this->adminUserActivateService->activateStatus($request->get('uuid'), $request->getClientIp());

        return $this->successResponse('Admin user has been reactivated successfully.');
    }
}
