<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use Modules\Product\App\Http\Requests\ProductCreateRequest;
use Modules\Product\DTO\ProductCreateDTO;
use Modules\Product\Service\Admin\ProductCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProductCreateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/products/create",
     *     summary="Create a new product",
     *     description="Create a new product with the provided details",
     *     operationId="createProduct",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="productName", type="string", example="Iphone XR"),
     *                 @OA\Property(property="url", type="string", example="Iphone-xr"),
     *                 @OA\Property(property="originalPrice", type="number", format="float", example="25000.00"),
     *                 @OA\Property(property="specialPrice", type="number", format="float", example="24500.00"),
     *                 @OA\Property(property="specialPriceStart", type="string", format="date-time", example="2024-04-05 05:55:05"),
     *                 @OA\Property(property="specialPriceEnd", type="string", format="date-time", example="2024-04-25 05:55:05"),
     *                 @OA\Property(property="sku", type="string", example="IPHONEXR"),
     *                 @OA\Property(property="description", type="string", example="Best apple phone available in the market."),
     *                 @OA\Property(property="additionalDescription", type="string", example="Beat the trend."),
     *                 @OA\Property(property="status", type="boolean", example="1"),
     *                 @OA\Property(property="onSale", type="boolean", example="1"),
     *                 @OA\Property(property="quantity", type="integer", example="100"),
     *                 @OA\Property(property="inStock", type="boolean", example="1"),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="integer"), example="[1]"),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="integer"), example="[1]"),
     *                 @OA\Property(property="files", type="object",
     *                     @OA\Property(property="baseImage", type="integer", example="2"),
     *                     @OA\Property(property="additionalImage", type="array", @OA\Items(type="integer"), example="[3,4]")
     *                 ),
     *                 @OA\Property(property="options", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="name", type="string", example="Color"),
     *                         @OA\Property(property="isColor", type="boolean", example="1"),
     *                         @OA\Property(property="values", type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="optionName", type="string", example="Black"),
     *                                 @OA\Property(property="optionData", type="string", example="#000000"),
     *                                 @OA\Property(property="files", type="object",
     *                                     @OA\Property(property="baseImage", type="integer", example="2"),
     *                                     @OA\Property(property="additionalImage", type="array", @OA\Items(type="integer"), example="[3,4]")
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="variants", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="name", type="string", example="Iphone XR-Black-2GB-64GB"),
     *                         @OA\Property(property="optionData1", type="string", example="#000000"),
     *                         @OA\Property(property="optionName1", type="string", example="Black"),
     *                         @OA\Property(property="optionName2", type="string", example="2GB"),
     *                         @OA\Property(property="optionName3", type="string", example="64GB"),
     *                         @OA\Property(property="sku", type="string", example="IphoneXR-Black-2GB-64GB"),
     *                         @OA\Property(property="status", type="boolean", example="1"),
     *                         @OA\Property(property="originalPrice", type="number", format="float", example="25000.00"),
     *                         @OA\Property(property="specialPrice", type="number", format="float", example="24500.00"),
     *                         @OA\Property(property="specialPriceStart", type="string", format="date-time", example="2024-04-05 05:55:05"),
     *                         @OA\Property(property="specialPriceEnd", type="string", format="date-time", example="2024-04-25 05:55:05"),
     *                         @OA\Property(property="quantity", type="integer", example="100"),
     *                         @OA\Property(property="inStock", type="boolean", example="1")
     *                     )
     *                 ),
     *                 @OA\Property(property="meta", type="object",
     *                     @OA\Property(property="metaTitle", type="string", example="iphone-xr"),
     *                     @OA\Property(property="keywords", type="array", @OA\Items(type="string", example="iphone")),
     *                     @OA\Property(property="metaDescription", type="string", example="Iphone XR")
     *                 ),
     *                 @OA\Property(property="attributes", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="attributeId", type="integer", example="2"),
     *                         @OA\Property(property="values", type="array", @OA\Items(type="integer"), example="[3,4]")
     *                     )
     *                 ),
     *                 @OA\Property(property="newFrom", type="string", format="date", example="2024-04-05"),
     *                 @OA\Property(property="newTo", type="string", format="date", example="2024-04-24"),
     *                 @OA\Property(property="relatedProducts", type="array", @OA\Items(type="string"), example={"54b9a26b-15bd-48d5-8dba-3fa532aaa184", "9e5c887e-8eed-4d01-937c-7930a488350a"}),
     *                 @OA\Property(property="upSells", type="array", @OA\Items(type="string"), example={"54b9a26b-15bd-48d5-8dba-3fa532aaa184"}),
     *                 @OA\Property(property="crossSells", type="array", @OA\Items(type="string"), example={"54b9a26b-15bd-48d5-8dba-3fa532aaa184"})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product has been created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product has been created successfully.")
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

    function __construct(private ProductCreateService $productCreateService)
    {
    }

    function __invoke(ProductCreateRequest $request)
    {
        $productCreateDTO = ProductCreateDTO::from($request->all());

        $this->productCreateService->create($productCreateDTO, $request->getClientIp());

        return $this->successResponse('Product has been created successfully.');
    }
}
