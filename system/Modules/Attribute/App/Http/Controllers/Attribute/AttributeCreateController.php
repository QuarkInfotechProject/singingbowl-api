<?php

namespace Modules\Attribute\App\Http\Controllers\Attribute;

use Modules\Attribute\App\Http\Requests\Attribute\AttributeCreateRequest;
use Modules\Attribute\Service\Attribute\AttributeCreateService;
use Modules\Attribute\Service\Attribute\DTO\AttributeCreateDTO;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AttributeCreateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/attributes/create",
     *     summary="Create an attribute",
     *     description="Create a new attribute with the provided details",
     *     operationId="createAttribute",
     *     tags={"Attributes"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Attribute details",
     *         @OA\JsonContent(
     *             required={"attributeSetId", "name"},
     *             @OA\Property(property="attributeSetId", type="string", example="2"),
     *             @OA\Property(property="name", type="string", example="Battery"),
     *             @OA\Property(property="url", type="string", example="battery"),
     *             @OA\Property(property="values", type="array", @OA\Items(type="string", example="value1")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Attribute has been created successfully."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AttributeCreateService $attributeCreateService)
    {
    }

    function __invoke(AttributeCreateRequest $request)
    {
        $attributeCreateDTO = AttributeCreateDTO::from($request->all());

        $this->attributeCreateService->create($attributeCreateDTO, $request->getClientIp());

        return $this->successResponse('Attribute has been created successfully.');
    }
}
