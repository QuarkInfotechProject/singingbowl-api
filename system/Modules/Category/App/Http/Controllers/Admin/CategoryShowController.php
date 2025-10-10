<?php

namespace Modules\Category\App\Http\Controllers\Admin;

use Modules\Category\Service\Admin\CategoryShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoryShowController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/categories/show/{id}",
     *     summary="Show a category",
     *     description="Show details of a category by its ID",
     *     operationId="showCategory",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the category",
     *         @OA\Schema(
     *             type="string",
     *             example="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=20),
     *                 @OA\Property(property="name", type="string", example="Real Madrid"),
     *                 @OA\Property(property="searchable", type="integer", example=1),
     *                 @OA\Property(property="active", type="integer", example=1),
     *                 @OA\Property(property="slug", type="string", example="madrid"),
     *                 @OA\Property(property="parentId", type="integer", example=19),
     *                 @OA\Property(
     *                     property="files",
     *                     type="object",
     *                     @OA\Property(
     *                         property="logo",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="url", type="string", example="http://localhost:98/files/070330170320246iELaA.jpg")
     *                     ),
     *                     @OA\Property(
     *                         property="banner",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=6),
     *                         @OA\Property(property="url", type="string", example="http://localhost:98/files/07033017032024jo0jE8.png")
     *                     )
     *                 )
     *             )
     *         )
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
    function __construct(private CategoryShowService $categoryShowService)
    {
    }

    function __invoke(int $id)
    {
        $category = $this->categoryShowService->show($id);

        return $this->successResponse('Category has been fetched successfully.', $category);
    }
}
