<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use Modules\Product\Service\Admin\ProductShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProductShowController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/products/show/{uuid}",
     *     summary="Fetch a specific product by UUID",
     *     description="Fetch a specific product based on the provided UUID",
     *     operationId="fetchProductByUUID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID of the product to fetch",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product has been fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="productName", type="string", example="Iphone XR"),
     *             @OA\Property(property="url", type="string", example="iphone-xr"),
     *             @OA\Property(property="originalPrice", type="string", example="25000.00"),
     *             @OA\Property(property="specialPrice", type="string", example="24500.00"),
     *             @OA\Property(property="specialPriceStart", type="string", format="date-time", example="2024-04-15 05:55:05"),
     *             @OA\Property(property="specialPriceEnd", type="string", format="date-time", example="2024-04-25 05:55:05"),
     *             @OA\Property(property="sku", type="string", example="IPHONEXR"),
     *             @OA\Property(property="description", type="string", example="Best apple phone available in the market."),
     *             @OA\Property(property="additionalDescription", type="string", example="Beat the heat."),
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="onSale", type="integer", example="1"),
     *             @OA\Property(property="quantity", type="integer", example="100"),
     *             @OA\Property(property="inStock", type="integer", example="1"),
     *             @OA\Property(property="newFrom", type="string", format="date", example=null),
     *             @OA\Property(property="newTo", type="string", format="date", example=null),
     *             @OA\Property(property="categories", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example="1"),
     *                     @OA\Property(property="name", type="string", example="Earbuds")
     *                 )
     *             ),
     *             @OA\Property(property="files", type="object",
     *                 @OA\Property(property="baseImage", type="object",
     *                     @OA\Property(property="id", type="integer", example="2"),
     *                     @OA\Property(property="baseImageUrl", type="string", example="http://localhost:98/modules/files/071242090420249ujFTj.jpg")
     *                 ),
     *                 @OA\Property(property="additionalImage", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example="3"),
     *                         @OA\Property(property="additionalImageUrl", type="string", example="http://localhost:98/modules/files/07124309042024ZLGAFV.jpg")
     *                     ),
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example="4"),
     *                         @OA\Property(property="additionalImageUrl", type="string", example="http://localhost:98/modules/files/07124309042024eCnZdv.jpg")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="options", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="uuid", type="string", example="53561edb-3828-4b52-a79f-96c7a2e9cf47"),
     *                     @OA\Property(property="name", type="string", example="Color"),
     *                     @OA\Property(property="values", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="uuid", type="string", example="35a2893a-18a8-4b6b-89c2-85f347c407cc"),
     *                             @OA\Property(property="optionName", type="string", example="Black"),
     *                             @OA\Property(property="optionData", type="string", example="#000000"),
     *                             @OA\Property(property="files", type="object",
     *                                 @OA\Property(property="baseImage", type="object",
     *                                     @OA\Property(property="id", type="integer", example="2"),
     *                                     @OA\Property(property="url", type="string", example="http://localhost:98/modules/files/Thumbnail/071242090420249ujFTj.jpg")
     *                                 ),
     *                                 @OA\Property(property="additionalImage", type="array",
     *                                     @OA\Items(
     *                                         @OA\Property(property="id", type="integer", example="3"),
     *                                         @OA\Property(property="url", type="string", example="http://localhost:98/modules/files/Thumbnail/07124309042024ZLGAFV.jpg")
     *                                     ),
     *                                     @OA\Items(
     *                                         @OA\Property(property="id", type="integer", example="4"),
     *                                         @OA\Property(property="url", type="string", example="http://localhost:98/modules/files/Thumbnail/07124309042024eCnZdv.jpg")
     *                                     )
     *                                 )
     *                             )
     *                         ),
     *                         @OA\Items(
     *                             @OA\Property(property="uuid", type="string", example="26f8bf6a-a223-4bea-9aad-4f01fd7259f9"),
     *                             @OA\Property(property="optionName", type="string", example="White"),
     *                             @OA\Property(property="optionData", type="string", example="#000000"),
     *                             @OA\Property(property="files", type="object",
     *                                 @OA\Property(property="baseImage", type="object",
     *                                     @OA\Property(property="id", type="integer", example="4"),
     *                                     @OA\Property(property="url", type="string", example="http://localhost:98/modules/files/Thumbnail/07124309042024eCnZdv.jpg")
     *                                 ),
     *                                 @OA\Property(property="additionalImage", type="array", example="[]")
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example="59"),
     *                     @OA\Property(property="uuid", type="string", example="c7ef0d47-db65-4dec-9c64-354abb2b2828"),
     *                     @OA\Property(property="name", type="string", example="Size"),
     *                     @OA\Property(property="values", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="optionName", type="string", example="2GB"),
     *                         ),
     *                         @OA\Items(
     *                             @OA\Property(property="optionName", type="string", example="4GB"),
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="variants", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="uuid", type="string", example="3dbc33f6-b6bb-412f-a31b-81a2e0fcc569"),
     *                     @OA\Property(property="optionName1", type="string", example="Black"),
     *                     @OA\Property(property="optionName2", type="string", example="2GB"),
     *                     @OA\Property(property="optionName3", type="string", example=null),
     *                     @OA\Property(property="status", type="integer", example="1"),
     *                     @OA\Property(property="quantity", type="integer", example="100"),
     *                     @OA\Property(property="originalPrice", type="integer", example="15000"),
     *                     @OA\Property(property="specialPrice", type="integer", example="10000")
     *                 ),
     *                 @OA\Items(
     *                     @OA\Property(property="uuid", type="string", example="0f15c380-42fa-4808-9c73-f40c34f13555"),
     *                     @OA\Property(property="optionName1", type="string", example="Black"),
     *                     @OA\Property(property="optionName2", type="string", example="4GB"),
     *                     @OA\Property(property="optionName3", type="string", example=null),
     *                     @OA\Property(property="status", type="integer", example="1"),
     *                     @OA\Property(property="quantity", type="integer", example="100"),
     *                     @OA\Property(property="originalPrice", type="integer", example="15000"),
     *                     @OA\Property(property="specialPrice", type="integer", example="10000")
     *                 ),
     *                 @OA\Items(
     *                     @OA\Property(property="uuid", type="string", example="b74937ee-5dad-4fa8-af79-dcc14fe035a1"),
     *                     @OA\Property(property="optionName1", type="string", example="White"),
     *                     @OA\Property(property="optionName2", type="string", example="2GB"),
     *                     @OA\Property(property="optionName3", type="string", example=null),
     *                     @OA\Property(property="status", type="integer", example="1"),
     *                     @OA\Property(property="quantity", type="integer", example="100"),
     *                     @OA\Property(property="originalPrice", type="integer", example="15000"),
     *                     @OA\Property(property="specialPrice", type="integer", example="10000")
     *                 ),
     *                 @OA\Items(
     *                     @OA\Property(property="uuid", type="string", example="56078857-ad74-4f9c-99b9-690fe1c2f9d3"),
     *                     @OA\Property(property="optionName1", type="string", example="White"),
     *                     @OA\Property(property="optionName2", type="string", example="4GB"),
     *                     @OA\Property(property="optionName3", type="string", example=null),
     *                     @OA\Property(property="status", type="integer", example="1"),
     *                     @OA\Property(property="quantity", type="integer", example="100"),
     *                     @OA\Property(property="originalPrice", type="integer", example="15000"),
     *                     @OA\Property(property="specialPrice", type="integer", example="10000")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="metaTitle", type="string", example="iphone-xr"),
     *                 @OA\Property(property="keywords", type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="iphone"
     *                     ),
     *                     @OA\Items(
     *                         type="string",
     *                         example="apple"
     *                     )
     *                 ),
     *                 @OA\Property(property="metaDescription", type="string", example="Iphone-XR")
     *             ),
     *             @OA\Property(property="attributes", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example="1"),
     *                     @OA\Property(property="name", type="string", example="Brand"),
     *                     @OA\Property(property="values", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example="1"),
     *                             @OA\Property(property="name", type="string", example="Apple")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="relatedProducts", type="array", nullable=true,
     *                  @OA\Items(
     *                      @OA\Property(property="uuid", type="string", example="b681aeac-e711-4c1e-a9b0-3286171d29de"),
     *                      @OA\Property(property="name", type="string", example="Iphone X"),
     *                      @OA\Property(property="price", type="string", example="25000.00"),
     *                      @OA\Property(property="status", type="boolean", example=true),
     *                      @OA\Property(property="baseImage", type="string", example="http://localhost:98/modules/files/Thumbnail/071242090420249ujFTj.jpg")
     *                  )
     *             ),
     *              @OA\Property(property="upSellProducts", type="array", nullable=true,
     *                  @OA\Items(
     *                      @OA\Property(property="uuid", type="string", example="b681aeac-e711-4c1e-a9b0-3286171d29de"),
     *                      @OA\Property(property="name", type="string", example="Iphone X"),
     *                      @OA\Property(property="price", type="string", example="25000.00"),
     *                      @OA\Property(property="status", type="boolean", example=true),
     *                      @OA\Property(property="baseImage", type="string", example="http://localhost:98/modules/files/Thumbnail/071242090420249ujFTj.jpg")
     *                  )
     *              ),
     *              @OA\Property(property="crossSellProducts", type="array", nullable=true,
     *                  @OA\Items(
     *                      @OA\Property(property="uuid", type="string", example="b681aeac-e711-4c1e-a9b0-3286171d29de"),
     *                      @OA\Property(property="name", type="string", example="Iphone X"),
     *                      @OA\Property(property="price", type="string", example="25000.00"),
     *                      @OA\Property(property="status", type="boolean", example=true),
     *                      @OA\Property(property="baseImage", type="string", example="http://localhost:98/modules/files/Thumbnail/071242090420249ujFTj.jpg")
     *                  )
     *              )
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
    function __construct(private ProductShowService $productShowService)
    {
    }

    function __invoke(string $uuid)
    {
        $product = $this->productShowService->show($uuid);

        return $this->successResponse('Product has been fetched successfully.', $product);
    }
}
