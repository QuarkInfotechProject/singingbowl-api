<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Product\App\Http\Requests\ProductQuickUpdateRequest;
use Modules\Product\Service\Admin\ProductQuickUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProductQuickUpdateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/products/change-status",
     *     summary="Change the status of a product",
     *     description="Change the status of a product by its UUID",
     *     operationId="changeProductStatus",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product UUID",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="uuid", type="string", example="54b9a26b-15bd-48d5-8dba-3fa532aaa184")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product status has been changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product status has been changed successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized.")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function __construct(private ProductQuickUpdateService $productQuickUpdateService)
    {
    }

    public function __invoke(ProductQuickUpdateRequest $request)
    {
        $this->productQuickUpdateService->quickUpdate($request->all());

        return $this->successResponse('Product has been updated successfully.');
    }
}
