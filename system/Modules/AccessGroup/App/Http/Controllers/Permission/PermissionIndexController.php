<?php

namespace Modules\AccessGroup\App\Http\Controllers\Permission;

use Modules\AccessGroup\Service\Permission\PermissionIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class PermissionIndexController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/permission",
     *     summary="Fetch permissions related to a module",
     *     description="Retrieve permissions related to a module along with their details",
     *     operationId="getModulePermissions",
     *     tags={"Permission"},
     *     @OA\Response(
     *         response=200,
     *         description="Permissions has been fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permissions have been fetched successfully."),
     *             @OA\Property(property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=2
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="create_admin_user"
     *                     ),
     *                     @OA\Property(
     *                         property="section",
     *                         type="string",
     *                         example="Admin User"
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="string",
     *                         example="can create a new admin user"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized."
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private PermissionIndexService $permissionForModuleService)
    {
    }

    function __invoke()
    {
        $permissions = $this->permissionForModuleService->index();

        return $this->successResponse('Permissions has been fetched successfully,', $permissions);
    }
}
