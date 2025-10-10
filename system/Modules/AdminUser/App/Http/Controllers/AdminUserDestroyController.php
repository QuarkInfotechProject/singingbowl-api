<?php

namespace Modules\AdminUser\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\AdminUser\Service\AdminUserDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AdminUserDestroyController extends AdminBaseController
{
    /**
     * @OA\Post(
     *      path="/api/admin/users/destroy",
     *      operationId="destroyedAdminUser",
     *      tags={"Admin"},
     *      summary="Destroyed an individual admin user.",
     *      description="Destroyed an individual admin user based on the provided UUID.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="uuid", type="string", example="452afd6b-dc37-46dc-b82f-60ce00c86dab"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Admin user has been destroyed successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Admin user has been destroyed successfully."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found."),
     *          ),
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AdminUserDestroyService $adminUserDeleteService)
    {
    }

    function __invoke(Request $request)
    {
        $this->adminUserDeleteService->destroy($request->get('uuid'), $request->getClientIp());

        return $this->successResponse('Admin user has been deleted successfully.');
    }
}
