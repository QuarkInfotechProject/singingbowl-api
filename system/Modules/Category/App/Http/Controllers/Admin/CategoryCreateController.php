<?php

namespace Modules\Category\App\Http\Controllers\Admin;

use Modules\Category\App\Http\Requests\CategoryCreateRequest;
use Modules\Category\DTO\CategoryCreateDTO;
use Modules\Category\Service\Admin\CategoryCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoryCreateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/categories/create",
     *     summary="Create a category",
     *     description="Create a new category with the provided details",
     *     operationId="createCategory",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "url", "searchable", "status", "files", "parentId"},
     *                 @OA\Property(property="name", type="string", example="Electronics"),
     *                 @OA\Property(property="description", type="string", example="Category for electronic products"),
     *                 @OA\Property(property="url", type="string", example="electronics"),
     *                 @OA\Property(property="searchable", type="integer", example=1),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(
     *                     property="files",
     *                     type="object",
     *                     @OA\Property(property="logo", type="string", example="1"),
     *                     @OA\Property(property="banner", type="string", example="1")
     *                 ),
     *                 @OA\Property(property="parentId", type="string", example="0")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Category has been created successfully."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private CategoryCreateService $categoryCreateService)
    {
    }

    function __invoke(CategoryCreateRequest $request)
    {
        $categoryCreateDTO = CategoryCreateDTO::from($request->all());

        $this->categoryCreateService->create($categoryCreateDTO, $request->getClientIp());

        return $this->successResponse('Category has been created successfully.');
    }
}
