<?php

namespace Modules\AccessGroup\App\Http\Controllers\Role;

use Illuminate\Http\Request;
use Modules\AccessGroup\Service\Role\RoleCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class RoleCreateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/group/create",
     *     summary="Create a user group",
     *     description="Create a new user group with the provided group name",
     *     operationId="createUserGroup",
     *     tags={"User Group"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="groupName", type="string", example="superAdmin")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User group has been created successfully.",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="User group has been created successfully."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Role already exists",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="A role `superAdmin` already exists for guard `admin`."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function __construct(private RoleCreateService $roleCreateService)
    {
    }

    public function __invoke(Request $request)
    {
        $this->roleCreateService->create($request, $request->getClientIp());

        return $this->successResponse('User group has been created successfully.');
    }
}
