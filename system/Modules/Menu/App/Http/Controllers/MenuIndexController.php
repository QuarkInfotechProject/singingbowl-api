<?php

namespace Modules\Menu\App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Modules\Menu\Service\MenuIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class   MenuIndexController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/menu",
     *     summary="Get the list of all the available menus.",
     *     description="Retrieve information about the menus and its sub menus.",
     *     operationId="Menu",
     *     tags={"Admin Menu"},
     *     @OA\Response(
     *         response=200,
     *         description="Menu has been fetched successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="title", type="string", example="Products"),
     *             @OA\Property(property="sortOrder", type="integer", example="1"),
     *             @OA\Property(property="icon", type="string", example="fa fa product"),
     *             @OA\Property(property="url", type="string"),
     *             @OA\Property(property="status", type="integer", example="1"),
     *             @OA\Property(property="parentId", type="integer", example="0"),
     *             @OA\Property(
     *                 property="children",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example="2"),
     *                     @OA\Property(property="title", type="string", example="Catalog"),
     *                     @OA\Property(property="sortOrder", type="integer", example="1"),
     *                     @OA\Property(property="icon", type="string", example="fa fa catalog"),
     *                     @OA\Property(property="url", type="string", example="/products"),
     *                     @OA\Property(property="status", type="integer", example="1"),
     *                     @OA\Property(property="parentId", type="integer", example="1"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request - The request body is invalid.",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private MenuIndexService $menuIndexService)
    {
    }

    function __invoke()
    {
        $menus = $this->menuIndexService->index();

        return $this->successResponse('Menu has been fetched successfully.', $menus);
    }
}
