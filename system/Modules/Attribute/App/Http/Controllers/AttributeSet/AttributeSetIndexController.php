<?php

namespace Modules\Attribute\App\Http\Controllers\AttributeSet;

use Modules\Attribute\Service\AttributeSet\AttributeSetIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AttributeSetIndexController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/attribute-sets",
     *     summary="Fetch all attribute sets",
     *     description="Retrieve all attribute sets",
     *     operationId="fetchAttributeSets",
     *     tags={"Attribute Sets"},
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=25
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Attribute sets has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Hardware"),
     *                     @OA\Property(property="url", type="string", example="hardware"),
     *                     @OA\Property(property="created", type="string", example="2024-03-19 10:28:35")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private AttributeSetIndexService $attributeSetIndexService)
    {
    }

    function __invoke()
    {
        $attributeSets = $this->attributeSetIndexService->index();

        return $this->successResponse('Attribute sets has been fetched successfully.', $attributeSets);
    }
}
