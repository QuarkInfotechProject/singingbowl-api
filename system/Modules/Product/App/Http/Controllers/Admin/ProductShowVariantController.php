<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use Modules\Product\Service\Admin\ProductShowVariantService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProductShowVariantController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/products/variants/show/{uuid}",
     *     summary="Show a product variant",
     *     description="Retrieve details of a specific product variant by UUID",
     *     operationId="showProductVariant",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID of the product variant",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="3dbc33f6-b6bb-412f-a31b-81a2e0fcc569"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product variant has been fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="optionName1", type="string", example="Black"),
     *             @OA\Property(property="optionData1", type="string", example="#000000"),
     *             @OA\Property(property="optionName2", type="string", example="2GB"),
     *             @OA\Property(property="optionName3", type="string", example=null),
     *             @OA\Property(property="name", type="string", example="Iphone XR-Black-2GB"),
     *             @OA\Property(property="sku", type="string", example="IphoneXR-Black-2GB"),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="originalPrice", type="string", example="25000.00"),
     *             @OA\Property(property="specialPrice", type="string", example=null),
     *             @OA\Property(property="specialPriceStart", type="string", format="date-time", example="2024-04-15 05:55:05"),
     *             @OA\Property(property="specialPriceEnd", type="string", format="date-time", example="2024-04-25 05:55:05"),
     *             @OA\Property(property="quantity", type="integer", example=100),
     *             @OA\Property(property="inStock", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product variant not found.",
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
    function __construct(private ProductShowVariantService $productShowVariantService)
    {
    }

    function __invoke(string $uuid)
    {
       $productVariant = $this->productShowVariantService->show($uuid);

       return $this->successResponse('Product variant has been fetched successfully.', $productVariant);
    }
}
