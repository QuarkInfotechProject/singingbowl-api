<?php

namespace Modules\Attribute\App\Http\Controllers\Attribute;

use Modules\Attribute\Service\Attribute\AttributeShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AttributeShowController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/attributes/show/{id}",
     *     summary="Show a specific attribute",
     *     description="Show details of a specific attribute by its ID",
     *     operationId="showAttribute",
     *     tags={"Attributes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the attribute to show",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Attribute has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Cameras"),
     *                 @OA\Property(property="url", type="string", example="cameras"),
     *                 @OA\Property(property="attributeSetId", type="integer", example=2),
     *                 @OA\Property(
     *                     property="values",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=46),
     *                         @OA\Property(property="value", type="string", example="1041MP")
     *                     )
     *                 )
     *             )
     *         )
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
    function __construct(private AttributeShowService $attributeShowService)
    {
    }

     function __invoke(int $id)
     {
         $attribute = $this->attributeShowService->show($id);

         return $this->successResponse('Attribute has been fetched successfully.', $attribute);
     }
}
