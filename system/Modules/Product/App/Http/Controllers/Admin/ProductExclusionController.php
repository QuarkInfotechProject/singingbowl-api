<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Product\Service\Admin\ProductExclusionService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProductExclusionController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/products/exclude/{uuid}",
     *     summary="Exclude a product by UUID",
     *     description="Get details of a product excluding the one with the provided UUID",
     *     operationId="excludeProduct",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID of the product to exclude",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         description="Filter products by name",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product has been fetched successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="string", example="806f1a02-8ccd-4009-8375-f583abf18ea8"),
     *                 @OA\Property(property="name", type="string", example="Ultima Boom 141 ANC"),
     *                 @OA\Property(property="originalPrice", type="string", example="3499.00"),
     *                 @OA\Property(property="specialPrice", type="string", example="2600.00"),
     *                 @OA\Property(property="files", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="imageUrl", type="string", example="http://localhost:98/modules/files/Thumbnail/07124309042024ZLGAFV.jpg")
     *                     )
     *                 )
     *             )
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
    function __construct(private ProductExclusionService $productExclusionService)
    {
    }

    function __invoke(Request $request, string $uuid)
    {
        $products = $this->productExclusionService->excludeCurrentProduct($request->geT('name'), $uuid);

        return $this->successResponse('Product has been fetched successfully.', $products);
    }
}
