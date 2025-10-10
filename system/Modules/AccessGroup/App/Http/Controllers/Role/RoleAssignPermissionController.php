<?php

namespace Modules\AccessGroup\App\Http\Controllers\Role;

use Illuminate\Http\Request;
use Modules\AccessGroup\Service\Role\RoleAssignPermissionService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class RoleAssignPermissionController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/group/assign-module-permission",
     *     summary="Assign permissions related to a module to a user group",
     *     description="Assign permissions to a user group with the provided group ID and an array of permission IDs",
     *     operationId="assignModulePermissionToUserGroup",
     *     tags={"User Group"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="groupId", type="string", example="2"),
     *                 @OA\Property(property="permissionId", type="array", @OA\Items(type="string", example="11")),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission has been added successfully.",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Permission has been added successfully."))
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
    function __construct(private RoleAssignPermissionService $roleAssignPermissionService)
    {
    }

    function __invoke(Request $request)
    {
        $this->roleAssignPermissionService->assignPermission($request->all());

        return $this->successResponse('Permission has been added successfully.');
    }
}
