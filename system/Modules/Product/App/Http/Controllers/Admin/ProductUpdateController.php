<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use Modules\Product\App\Http\Requests\ProductUpdateRequest;
use Modules\Product\DTO\ProductUpdateDTO;
use Modules\Product\Service\Admin\ProductUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProductUpdateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/products/update",
     *     summary="Update a product",
     *     description="Update an existing product with the provided details",
     *     operationId="updateProduct",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="uuid", type="string", example="675ad774-245e-41a0-afda-34d0d2c38232"),
     *                 @OA\Property(property="productName", type="string", example="Iphone XR"),
     *                 @OA\Property(property="url", type="string", example="iphone-xr"),
     *                 @OA\Property(property="originalPrice", type="string", example="25000"),
     *                 @OA\Property(property="specialPrice", type="string", example="24500"),
     *                 @OA\Property(property="specialPriceStart", type="string", format="date-time", example="2024-04-15 05:55:05"),
     *                 @OA\Property(property="specialPriceEnd", type="string", format="date-time", example="2024-04-25 05:55:05"),
     *                 @OA\Property(property="sku", type="string", example="IPHONEXR"),
     *                 @OA\Property(property="description", type="string", example="Best apple phone available in the market."),
     *                 @OA\Property(property="additionalDescription", type="string", example="Beat the heat."),
     *                 @OA\Property(property="status", type="string", example="1"),
     *                 @OA\Property(property="onSale", type="string", example="1"),
     *                 @OA\Property(property="quantity", type="string", example="100"),
     *                 @OA\Property(property="inStock", type="string", example="1"),
     *                 @OA\Property(property="newFrom", type="string", format="date", example=""),
     *                 @OA\Property(property="newTo", type="string", format="date", example=""),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="string"), example="['1']"),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="string"), example="['1']"),
     *                 @OA\Property(property="files", type="object",
     *                     @OA\Property(property="baseImage", type="string", example="2"),
     *                     @OA\Property(property="additionalImage", type="array", @OA\Items(type="string"), example="['3', '4']")
     *                 ),
     *                 @OA\Property(property="options", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="uuid", type="string", example="53561edb-3828-4b52-a79f-96c7a2e9cf47"),
     *                         @OA\Property(property="name", type="string", example="Color"),
     *                         @OA\Property(property="isColor", type="string", example="1"),
     *                         @OA\Property(property="values", type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="uuid", type="string", example="35a2893a-18a8-4b6b-89c2-85f347c407cc"),
     *                                 @OA\Property(property="optionName", type="string", example="Black"),
     *                                 @OA\Property(property="optionData", type="string", example="#000000"),
     *                                 @OA\Property(property="files", type="object",
     *                                     @OA\Property(property="baseImage", type="string", example="2"),
     *                                     @OA\Property(property="additionalImage", type="array", @OA\Items(type="string"), example="['3', '4']")
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="variants", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="name", type="string", example="Iphone XR-Black-2GB"),
     *                         @OA\Property(property="optionData1", type="string", example="#000000"),
     *                         @OA\Property(property="optionName1", type="string", example="Black"),
     *                         @OA\Property(property="optionName2", type="string", example="2GB"),
     *                         @OA\Property(property="optionName3", type="string", example=""),
     *                         @OA\Property(property="sku", type="string", example="IphoneXR-Black-2GB"),
     *                         @OA\Property(property="status", type="string", example="1"),
     *                         @OA\Property(property="originalPrice", type="string", example="25000"),
     *                         @OA\Property(property="specialPrice", type="string", example=""),
     *                         @OA\Property(property="quantity", type="string", example="100"),
     *                         @OA\Property(property="inStock", type="string", example="1")
     *                     )
     *                 ),
     *                 @OA\Property(property="meta", type="object",
     *                     @OA\Property(property="metaTitle", type="string", example="iphone-xr"),
     *                     @OA\Property(property="keywords", type="array", @OA\Items(type="string"), example="['iphone', 'apple']"),
     *                     @OA\Property(property="metaDescription", type="string", example="Iphone-XR")
     *                 ),
     *                 @OA\Property(property="attributes", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", example="7"),
     *                         @OA\Property(property="attributeId", type="string", example="1"),
     *                         @OA\Property(property="values", type="array", @OA\Items(type="string"), example="['1']")
     *                     )
     *                 ),
     *                 @OA\Property(property="relatedProducts", type="array", @OA\Items(type="string"), example={"54b9a26b-15bd-48d5-8dba-3fa532aaa184", "9e5c887e-8eed-4d01-937c-7930a488350a"}),
     *                 @OA\Property(property="upSells", type="array", @OA\Items(type="string"), example={"54b9a26b-15bd-48d5-8dba-3fa532aaa184"}),
     *                 @OA\Property(property="crossSells", type="array", @OA\Items(type="string"), example={"54b9a26b-15bd-48d5-8dba-3fa532aaa184"})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product has been updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product has been updated successfully.")
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
    function __construct(private ProductUpdateService $productUpdateService)
    {
    }

    function __invoke(ProductUpdateRequest $request)
    {
        $productUpdateDTO = ProductUpdateDTO::from($request->all());

        $this->productUpdateService->update($productUpdateDTO, $request->getClientIp());

        return $this->successResponse('Product has been updated successfully.');
    }
}
