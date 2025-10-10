<?php

namespace Modules\AccessGroup\App\Http\Controllers\Role;

use Illuminate\Http\Request;
use Modules\AccessGroup\Service\Role\RoleUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class RoleUpdateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/group/update",
     *     summary="Update a user group",
     *     description="Update a user group by its ID with the provided group name",
     *     operationId="updateUserGroup",
     *     tags={"User Group"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="id", type="string", example="1"),
     *                 @OA\Property(property="groupName", type="string", example="Admin")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User group has been updated successfully.",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="User group has been updated successfully."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User group not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="User group not found."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private RoleUpdateService $roleUpdateService)
    {

    }

    function __invoke(Request $request)
    {
        $this->roleUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('User group has been updated successfully.');
    }
}
