<?php

namespace Modules\AdminUser\App\Http\Controllers;

use Modules\AdminUser\App\Http\Requests\AdminUserUpdateRequest;
use Modules\AdminUser\DTO\AdminUserUpdateDTO;
use Modules\AdminUser\Service\AdminUserUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AdminUserUpdateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *      path="/api/admin/users/update",
     *      operationId="updateAdminUser",
     *      tags={"Admin"},
     *      summary="Update an existing admin user.",
     *      description="Updates an existing admin user with the provided UUID.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"uuid", "name", "status"},
     *              @OA\Property(property="uuid", type="string", example="c6c38d6b-ec5f-4452-a000-7ba220c6a59a", description="Admin user UUID"),
     *              @OA\Property(property="name", type="string", example="admin", description="Updated admin user's name"),
     *              @OA\Property(property="status", type="integer", example=1, description="Updated admin user's status"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Admin user has been updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Admin user has been updated successfully."),
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
    function __construct(private AdminUserUpdateService $adminUserUpdateService)
    {
    }

    function __invoke(AdminUserUpdateRequest $request)
    {
        $adminUserUpdateDTO = AdminUserUpdateDTO::from($request->all());

        $this->adminUserUpdateService->update($adminUserUpdateDTO, $request->getClientIp());

        return $this->successResponse('Admin user has been updated successfully.');
    }
}
