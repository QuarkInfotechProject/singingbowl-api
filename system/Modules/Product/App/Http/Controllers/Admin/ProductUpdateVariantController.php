<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use Modules\Product\App\Http\Requests\ProductVariantUpdateRequest;
use Modules\Product\DTO\ProductUpdateVariantDTO;
use Modules\Product\Service\Admin\ProductUpdateVariantService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProductUpdateVariantController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/products/variants/update",
     *     summary="Update a product variant",
     *     description="Update details of a specific product variant",
     *     operationId="updateProductVariant",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product variant details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="productUuid", type="string", format="uuid", example="5009f752-c264-4ee2-b13e-d928419bd757"),
     *                 @OA\Property(property="variantUuid", type="string", format="uuid", example="eb81fc2e-2e00-489d-9b8b-e0f3c983b2463"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="originalPrice", type="string", example="2500"),
     *                 @OA\Property(property="specialPrice", type="string", example="1500"),
     *                 @OA\Property(property="specialPriceStart", type="string", format="date-time", example="2024-04-15 05:55:05"),
     *                 @OA\Property(property="specialPriceEnd", type="string", format="date-time", example="2024-04-25 05:55:05"),
     *                 @OA\Property(property="quantity", type="string", example="50"),
     *                 @OA\Property(property="inStock", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product variant has been updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product variant has been updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product variant not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product variant not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    function __construct(private ProductUpdateVariantService $productUpdateVariantService)
    {
    }

    function __invoke(ProductVariantUpdateRequest $request)
    {
        $productUpdateVariantDTO = ProductUpdateVariantDTO::from($request->all());

        $this->productUpdateVariantService->update($productUpdateVariantDTO);

        return $this->successResponse('Product variant has been updated successfully.');
    }
}
