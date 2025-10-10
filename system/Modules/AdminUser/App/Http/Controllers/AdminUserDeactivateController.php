<?php

namespace Modules\AdminUser\App\Http\Controllers;

use Modules\AdminUser\App\Http\Requests\AdminUserDeactivateRequest;
use Modules\AdminUser\DTO\AdminUserDeactivateDTO;
use Modules\AdminUser\Service\AdminUserDeactivateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AdminUserDeactivateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *      path="/api/admin/users/deactivate",
     *      operationId="deactivateAdminUser",
     *      tags={"Admin"},
     *      summary="Deactivate an individual admin user.",
     *      description="Deactivate an individual admin user based on the provided UUID.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Admin user deactivation data",
     *          @OA\JsonContent(
     *              @OA\Property(property="uuid", type="string"),
     *              @OA\Property(property="remarks", type="string"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Admin user has been deactivated successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Admin user has been deactivated successfully."),
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
     *            response=404,
     *            description="User not found.",
     *            @OA\JsonContent(
     *                @OA\Property(property="message", type="string", example="User not found."),
     *            ),
     *        ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AdminUserDeactivateService $adminUserDeactivateService)
    {
    }

    function __invoke(AdminUserDeactivateRequest $request)
    {
        $adminUserDeactivateDTO = AdminUserDeactivateDTO::from($request->all());

        $this->adminUserDeactivateService->deactivateStatus($adminUserDeactivateDTO, $request->getClientIp());

        return $this->successResponse('Admin user has been deactivated successfully.');
    }
}
