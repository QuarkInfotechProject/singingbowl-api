<?php

namespace Modules\Category\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Category\Service\Admin\CategoryDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoryDestroyController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/categories/destroy",
     *     summary="Delete a category",
     *     description="Delete a category by its ID",
     *     operationId="deleteCategory",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property(property="id", type="string", example="1")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Category has been deleted successfully."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Category not found."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private CategoryDestroyService $categoryDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->categoryDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Category has been deleted successfully.');
    }
}
