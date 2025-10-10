<?php

namespace Modules\AdminUser\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\AdminUser\Service\AdminUserShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AdminUserShowController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/users/show/{uuid}",
     *     operationId="viewAdminUser",
     *     tags={"Admin"},
     *     summary="View an individual admin user",
     *     description="View an individual admin user by providing the user UUID in the URL",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Admin user UUID",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Admin user has been fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin user has been fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="uuid", type="string", format="uuid"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="status", type="integer"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User is already deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found."),
     *         ),
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AdminUserShowService $adminUserShowService)
    {

    }

    function __invoke(string $uuid)
    {
        $user = $this->adminUserShowService->show($uuid);

        return $this->successResponse('Admin user has been fetched successfully.', $user);
    }
}
