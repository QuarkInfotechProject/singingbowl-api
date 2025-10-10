<?php

namespace Modules\Menu\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Menu\Service\MenuReOrderService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class MenuReOrderController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/menu/reorder",
     *     summary="Reorder a menu item",
     *     description="Reorder a menu item by its ID and new sort order",
     *     operationId="reorderMenu",
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
     *                 @OA\Property(
     *                     property="sortOrder",
     *                     type="string",
     *                     example="2"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Menu has been reordered successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Menu has been reordered successfully."
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
    function __construct(private MenuReOrderService $menuReOrderService)
    {
    }

    function __invoke(Request $request)
    {
        $this->menuReOrderService->reOrder($request->get('id'), $request->get('sortOrder'));

        return $this->successResponse('Menu has been reordered successfully.');
    }
}
