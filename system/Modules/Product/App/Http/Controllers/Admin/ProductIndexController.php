<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Product\DTO\ProductFilterDTO;
use Modules\Product\Service\Admin\ProductIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProductIndexController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/products",
     *     summary="Fetch all products with filtering options",
     *     description="Fetch all products with filtering options based on provided attributes",
     *     operationId="fetchProducts",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Filtering options",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Iphone"),
     *                 @OA\Property(property="status", type="integer", example="1"),
     *                 @OA\Property(property="sku", type="string", example="IPHONE"),
     *                 @OA\Property(property="sortBy", type="string", enum={"name", "price"}, example="name"),
     *                 @OA\Property(property="sortDirection", type="string", enum={"asc", "desc"}, example="asc")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Products has been fetched successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="string", example="675ad774-245e-41a0-afda-34d0d2c38232"),
     *                 @OA\Property(property="name", type="string", example="Iphone XR"),
     *                 @OA\Property(property="sku", type="string", example="IphoneXR"),
     *                 @OA\Property(property="originalPrice", type="string", example="25000.00"),
     *                 @OA\Property(property="specialPrice", type="string", example="24500.00"),
     *                 @OA\Property(property="variantCount", type="integer", example="8"),
     *                 @OA\Property(property="files", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="imageUrl", type="string", example="http://localhost:98/modules/files/Thumbnail/071242090420249ujFTj.jpg")
     *                     )
     *                 )
     *             )
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
    function __construct(private ProductIndexService $productIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $productFilterDTO = ProductFilterDTO::from($request->all());

        $products = $this->productIndexService->index($productFilterDTO);

        return $this->successResponse('Product has been fetched successfully.', $products);
    }
}
