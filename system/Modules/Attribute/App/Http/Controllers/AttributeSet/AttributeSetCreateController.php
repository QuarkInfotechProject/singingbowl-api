<?php

namespace Modules\Attribute\App\Http\Controllers\AttributeSet;

use Modules\Attribute\App\Http\Requests\AttributeSet\AttributeSetCreateRequest;
use Modules\Attribute\Service\AttributeSet\AttributeSetCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AttributeSetCreateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/attribute-sets/create",
     *     summary="Create a attribute set",
     *     description="Create a new attribute set with the provided name and URL",
     *     operationId="createAttributeSet",
     *     tags={"Attribute Sets"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "url"},
     *                 @OA\Property(property="name", type="string", example="Hardware"),
     *                 @OA\Property(property="url", type="string", example="hardware")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Attribute set has been created successfully."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AttributeSetCreateService $attributeSetCreateService)
    {
    }

    function __invoke(AttributeSetCreateRequest $request)
    {
        $this->attributeSetCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('Attribute set has been created successfully.');
    }
}
