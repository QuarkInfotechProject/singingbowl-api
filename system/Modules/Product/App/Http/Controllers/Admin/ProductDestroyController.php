<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Product\Service\Admin\ProductDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProductDestroyController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/products/destroy",
     *     summary="Delete a product",
     *     description="Delete a specific product by its UUID",
     *     operationId="deleteProduct",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="UUID of the product to delete",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="uuid", type="string", format="uuid", example="53fbb7f4-8f29-4062-897e-d9445268b0a8")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product has been deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product has been deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
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
    function __construct(private ProductDestroyService $productDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->productDestroyService->destroy($request->get('uuid'), $request->getClientIp());

        return $this->successResponse('Product has been deleted successfully.');
    }
}
