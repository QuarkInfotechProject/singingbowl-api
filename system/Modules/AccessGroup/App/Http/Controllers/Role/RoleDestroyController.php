<?php

namespace Modules\AccessGroup\App\Http\Controllers\Role;

use Illuminate\Http\Request;
use Modules\AccessGroup\Service\Role\RoleDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class RoleDestroyController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/group/destroy",
     *     summary="Delete a user group",
     *     description="Delete a user group by its ID",
     *     operationId="destroyUserGroup",
     *     tags={"User Group"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="id", type="string", example="1")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User group has been deleted successfully.",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="User group has been deleted successfully."))
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
    function __construct(private RoleDestroyService $roleDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->roleDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('User group has been deleted successfully.');
    }
}
