<?php

namespace Modules\Attribute\App\Http\Controllers\Attribute;

use Modules\Attribute\App\Http\Requests\Attribute\AttributeUpdateRequest;
use Modules\Attribute\Service\Attribute\AttributeUpdateService;
use Modules\Attribute\Service\Attribute\DTO\AttributeUpdateDTO;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AttributeUpdateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/attributes/update",
     *     summary="Update an attribute",
     *     description="Update details of an attribute",
     *     operationId="updateAttribute",
     *     tags={"Attributes"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Attribute details to update",
     *         @OA\JsonContent(
     *             required={"id", "attributeSetId", "name", "url", "values"},
     *             @OA\Property(property="id", type="string", example="2"),
     *             @OA\Property(property="attributeSetId", type="string", example="2"),
     *             @OA\Property(property="name", type="string", example="Camera"),
     *             @OA\Property(property="url", type="string", example="camera"),
     *             @OA\Property(
     *                 property="values",
     *                 type="array",
     *                 @OA\Items(
     *                     required={"id", "value"},
     *                     @OA\Property(property="id", type="string", example="1"),
     *                     @OA\Property(property="value", type="string", example="250MP")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Attribute has been updated successfully."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attribute not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Attribute not found."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AttributeUpdateService $attributeUpdateService)
    {
    }

    function __invoke(AttributeUpdateRequest $request)
    {
        $attributeUpdateDTO = AttributeUpdateDTO::from($request->all());

        $this->attributeUpdateService->update($attributeUpdateDTO, $request->getClientIp());

        return $this->successResponse('Attribute has been updated successfully.');
    }
}
