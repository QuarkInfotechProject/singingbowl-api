<?php

namespace Modules\AccessGroup\App\Http\Controllers\Role;

use Illuminate\Http\Request;
use Modules\AccessGroup\Service\Role\RolePermissionMappingService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class RolePermissionMappingController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/group/module-mapping",
     *     summary="Map permissions related to modules for a user group",
     *     description="Map permissions related to a modules for a user group by providing the group ID",
     *     operationId="mapModulePermissionsForUserGroup",
     *     tags={"User Group"},
     *     parameters={
     *         {
     *             "name": "groupId",
     *             "in": "query",
     *             "required": true,
     *             "description": "ID of the user group",
     *             "schema": {
     *                 "type": "string"
     *             }
     *         }
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Permission associated with the user group has been fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permission associated with the user group has been fetched successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="integer", example=9))
     *         )
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
    function __construct(private RolePermissionMappingService $rolePermissionMappingService)
    {
    }

    function __invoke(Request $request)
    {
        // Validate groupId as UUID
        $validated = $request->validate([
            'groupId' => 'required|uuid',
        ]);
        $permissions = $this->rolePermissionMappingService->index($validated['groupId']);

        return $this->successResponse('Permission associated with the user group has been fetched successfully.', $permissions);
    }
}
