<?php

namespace Modules\Tag\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Tag\App\Http\Requests\TagUpdateRequest;
use Modules\Tag\Service\TagUpdateService;

class TagUpdateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/tags/update",
     *     summary="Update a tag",
     *     description="Update details of a tag by its ID",
     *     operationId="updateTag",
     *     tags={"Tags"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "name", "url"},
     *                 @OA\Property(property="id", type="string", example="1"),
     *                 @OA\Property(property="name", type="string", example="Apple"),
     *                 @OA\Property(property="url", type="string", example="apple")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Tag has been updated successfully."))
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
    function __construct(private TagUpdateService $tagUpdateService)
    {
    }

    function __invoke(TagUpdateRequest $request)
    {
        $this->tagUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Tag has been updated successfully.');
    }
}
