<?php

namespace Modules\AdminUser\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminUser\DTO\AdminUserFilterDTO;
use Modules\AdminUser\Service\AdminUserIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AdminUserIndexController extends AdminBaseController
{
    /**
     * @OA\Post(
     *      path="/api/admin/users",
     *      operationId="listAdminUsers",
     *      tags={"Admin"},
     *      summary="List admin users based on filters",
     *      description="Get a list of admin users based on the provided filters.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Admin user filters",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="admin"),
     *              @OA\Property(property="status", type="integer", example=1),
     *              @OA\Property(property="email", type="string", format="email", example="admin@gmail.com"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Admin users have been fetched successfully.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Admin users have been fetched successfully."),
     *              @OA\Property(property="data", type="array", @OA\Items(
     *                  @OA\Property(property="uuid", type="string"),
     *                  @OA\Property(property="fullName", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="status", type="integer"),
     *              )),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          ),
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AdminUserIndexService $adminUserIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $adminUserFilterDTO = AdminUserFilterDTO::from($request->all());

        $users = $this->adminUserIndexService->index($adminUserFilterDTO);

        return $this->successResponse('Admin users have been fetched successfully.', $users);
    }
}
