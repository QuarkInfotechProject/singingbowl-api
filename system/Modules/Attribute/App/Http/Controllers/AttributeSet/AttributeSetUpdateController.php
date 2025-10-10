<?php

namespace Modules\Attribute\App\Http\Controllers\AttributeSet;

use Modules\Attribute\App\Http\Requests\AttributeSet\AttributeSetUpdateRequest;
use Modules\Attribute\Service\AttributeSet\AttributeSetUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AttributeSetUpdateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/attribute-sets/update",
     *     summary="Update a attribute set",
     *     description="Update details of a attribute set by its ID",
     *     operationId="updateAttributeSet",
     *     tags={"Attribute Sets"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "name", "url"},
     *                 @OA\Property(property="id", type="string", example="1"),
     *                 @OA\Property(property="name", type="string", example="Software"),
     *                 @OA\Property(property="url", type="string", example="software")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Attribute set has been updated successfully."))
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
    function __construct(private AttributeSetUpdateService $attributeSetUpdateService)
    {
    }

    function __invoke(AttributeSetUpdateRequest $request)
    {
        $this->attributeSetUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Attribute set has been updated successfully.');
    }
}
