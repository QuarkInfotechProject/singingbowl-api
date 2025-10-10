<?php

namespace Modules\Tag\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Tag\Service\TagDestroyService;

class TagDestroyController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/tags/destroy",
     *     summary="Delete a tag",
     *     description="Delete a tag by its ID",
     *     operationId="deleteTag",
     *     tags={"Tags"},
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
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Tag has been deleted successfully."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tag not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Tag not found."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private TagDestroyService $tagDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->tagDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Tag has been deleted successfully.');
    }
}
