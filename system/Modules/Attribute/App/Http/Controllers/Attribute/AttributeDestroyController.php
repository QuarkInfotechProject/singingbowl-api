<?php

namespace Modules\Attribute\App\Http\Controllers\Attribute;

use Illuminate\Http\Request;
use Modules\Attribute\Service\Attribute\AttributeDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AttributeDestroyController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/attributes/destroy",
     *     summary="Delete an attribute",
     *     description="Delete an attribute by its ID",
     *     operationId="deleteAttribute",
     *     tags={"Attributes"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Attribute ID to delete",
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="string", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Attribute has been deleted successfully."))
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
    function __construct(private AttributeDestroyService $attributeDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->attributeDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Attribute has been deleted successfully.');
    }
}
