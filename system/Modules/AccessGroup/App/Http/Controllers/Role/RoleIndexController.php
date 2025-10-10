<?php

namespace Modules\AccessGroup\App\Http\Controllers\Role;

use Illuminate\Http\Request;
use Modules\AccessGroup\Service\Role\RoleIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class RoleIndexController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/group",
     *     summary="Fetch all user groups",
     *     description="Retrieve all user groups along with their details",
     *     operationId="getUserGroups",
     *     tags={"User Group"},
     *     @OA\Response(
     *         response=200,
     *         description="User group has been fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User group has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="superAdmin"),
     *                     @OA\Property(property="createdAt", type="string", format="date-time", example="2024-02-20 12:00:39")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private RoleIndexService $roleIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $groups = $this->roleIndexService->index($request->get('group'));

        return $this->successResponse('User group has been fetched successfully.', $groups);
    }
}
