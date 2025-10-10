<?php

namespace Modules\AdminUser\App\Http\Controllers;

use Modules\AdminUser\App\Http\Requests\AdminUserCreateRequest;
use Modules\AdminUser\DTO\AdminUserCreateDTO;
use Modules\AdminUser\Service\AdminUserCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AdminUserCreateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *      path="/api/admin/users/create",
     *      operationId="createAdminUser",
     *      tags={"Admin"},
     *      summary="Create a new admin user.",
     *      description="Creates a new admin user with the provided data.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "email", "password"},
     *              @OA\Property(property="name", type="string", example="cto", description="Admin user's name"),
     *              @OA\Property(property="email", type="string", format="email", example="cto@gmail.com", description="Admin user's email"),
     *              @OA\Property(property="password", type="string", example="password123", description="Admin user's password"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Admin has been created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Admin user has been created successfully."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="The given data was invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The email has already been taken."),
     *          ),
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AdminUserCreateService $adminUserCreateService)
    {
    }

    function __invoke(AdminUserCreateRequest $request)
    {
        $adminUserCreateDTO = AdminUserCreateDTO::from($request->all());

        $this->adminUserCreateService->create($adminUserCreateDTO, $request->getClientIp());

        return $this->successResponse('Admin user has been created successfully.');
    }
}
