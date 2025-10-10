<?php

namespace Modules\Category\App\Http\Controllers\Admin;

use Modules\Category\Service\Admin\CategoryIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoryIndexController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/categories",
     *     summary="Fetch all categories",
     *     description="Retrieve all categories",
     *     operationId="fetchCategories",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Categories has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Electronics"),
     *                     @OA\Property(property="parentId", type="integer", example=0),
     *                     @OA\Property(
     *                         property="children",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="name", type="string", example="Mobiles"),
     *                             @OA\Property(property="parentId", type="integer", example=1),
     *                             @OA\Property(
     *                                 property="children",
     *                                 type="array",
     *                                 @OA\Items(
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=3),
     *                                     @OA\Property(property="name", type="string", example="Smartphones"),
     *                                     @OA\Property(property="parentId", type="integer", example=2)
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private CategoryIndexService $categoryIndexService)
    {
    }

    function __invoke()
    {
        $categories = $this->categoryIndexService->index();

        return $this->successResponse('Categories has been fetched successfully.', $categories);
    }
}
