<?php

namespace Modules\Attribute\App\Http\Controllers\AttributeSet;

use Illuminate\Http\Request;
use Modules\Attribute\Service\AttributeSet\AttributeSetDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AttributeSetDestroyController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/attribute-sets/destroy",
     *     summary="Delete a attribute set",
     *     description="Delete a attribute set by its ID",
     *     operationId="deleteAttributeSet",
     *     tags={"Attribute Sets"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property(property="id", type="string", example="1")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Attribute set has been deleted successfully."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attribute set not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Attribute set not found."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AttributeSetDestroyService $attributeSetDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->attributeSetDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Attribute set has been deleted successfully.');
    }
}
