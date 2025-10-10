<?php

namespace Modules\Menu\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Menu\Service\MenuChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class MenuChangeStatusController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/menu/change-status",
     *     summary="Change the status of a menu item",
     *     description="Change the status of a menu item by its ID",
     *     operationId="changeMenuStatus",
     *     tags={"Admin Menu"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="string",
     *                     example="1"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status of the menu has been changed successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Status of the menu has been changed successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Menu not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Menu not found."
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private MenuChangeStatusService $menuChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->menuChangeStatusService->changeStatus($request->get('id'));

        return $this->successResponse('Status of the menu has been changed successfully.');
    }
}
