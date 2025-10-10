<?php

namespace Modules\AccessGroup\App\Http\Controllers\Role;

use Illuminate\Http\Request;
use Modules\AccessGroup\Service\Role\RoleRevokePermissionService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class RoleRevokePermissionController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/group/revoke-permission",
     *     summary="Revoke a permission from a user group",
     *     description="Revoke a permission from a user group with the provided group ID and permission ID",
     *     operationId="revokePermissionFromUserGroup",
     *     tags={"User Group"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="groupId", type="string", example="2"),
     *                 @OA\Property(property="permissionId", type="string", example="26"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission has been removed successfully.",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Permission has been removed successfully."))
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
    function __construct(private RoleRevokePermissionService $roleRevokePermissionService)
    {
    }

    function __invoke(Request $request)
    {
        // Validate groupId and permissionId as UUIDs
        $validated = $request->validate([
            'groupId' => 'required|uuid',
            'permissionId' => 'required|uuid',
        ]);
        $this->roleRevokePermissionService->revokePermission($validated);

        return $this->successResponse('Permission has been removed successfully.');
    }
}
