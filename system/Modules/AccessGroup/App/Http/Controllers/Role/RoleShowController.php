<?php

namespace Modules\AccessGroup\App\Http\Controllers\Role;

use Illuminate\Http\Request;
use Modules\AccessGroup\Service\Role\RoleShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class RoleShowController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/group/show/{id}",
     *     summary="Show details of an individual user group",
     *     description="Show details of an individual user group by its ID",
     *     operationId="showUserGroup",
     *     tags={"User Group"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user group",
     *         @OA\Schema(
     *             type="string",
     *             example="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User group has been fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User group has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="superAdmin")
     *             )
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
    function __construct(private RoleShowService $roleShowService)
    {
    }

    function __invoke(int $id)
    {
        $group = $this->roleShowService->show($id);

        return $this->successResponse('User group has been fetched successfully.', $group);
    }
}
