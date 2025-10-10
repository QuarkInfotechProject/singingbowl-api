<?php

namespace Modules\Category\App\Http\Controllers\Admin;

use Modules\Category\App\Http\Requests\CategoryUpdateRequest;
use Modules\Category\DTO\CategoryUpdateDTO;
use Modules\Category\Service\Admin\CategoryUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoryUpdateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/categories/update",
     *     summary="Update a category",
     *     description="Update details of a category by its ID",
     *     operationId="updateCategory",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "name", "searchable", "status", "files", "parentId"},
     *                 @OA\Property(property="id", type="string", example="20"),
     *                 @OA\Property(property="name", type="string", example="Los Blancos"),
     *                 @OA\Property(property="description", type="string", example="Updated category description"),
     *                 @OA\Property(property="searchable", type="string", example="1"),
     *                 @OA\Property(property="status", type="string", example="1"),
     *                 @OA\Property(
     *                     property="files",
     *                     type="object",
     *                     @OA\Property(property="logo", type="string", example="5"),
     *                     @OA\Property(property="banner", type="string", example="6")
     *                 ),
     *                 @OA\Property(property="parentId", type="string", example="1")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Category has been updated successfully."))
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
    function __construct(private CategoryUpdateService $categoryUpdateService)
    {
    }

    function __invoke(CategoryUpdateRequest $request)
    {
        $categoryUpdateDTO = CategoryUpdateDTO::from($request->all());

        $this->categoryUpdateService->update($categoryUpdateDTO, $request->getClientIp());

        return $this->successResponse('Category has been updated successfully.');
    }
}
