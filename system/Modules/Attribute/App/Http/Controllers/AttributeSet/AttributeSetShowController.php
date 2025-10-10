<?php

namespace Modules\Attribute\App\Http\Controllers\AttributeSet;

use Modules\Attribute\Service\AttributeSet\AttributeSetShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AttributeSetShowController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/attribute-sets/show/{id}",
     *     summary="Show a attribute set",
     *     description="Show details of a attribute set by its ID",
     *     operationId="showAttributeSet",
     *     tags={"Attribute Sets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Attribute set ID",
     *         @OA\Schema(
     *             type="string",
     *             example="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Attribute set has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Hardware"),
     *                 @OA\Property(property="url", type="string", example="hardware")
     *             )
     *         )
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
    function __construct(private AttributeSetShowService $attributeSetShowService)
    {
    }

    function __invoke(int $id)
    {
        $attributeSet = $this->attributeSetShowService->show($id);

        return $this->successResponse('Attribute set has been fetched successfully.', $attributeSet);
    }
}
