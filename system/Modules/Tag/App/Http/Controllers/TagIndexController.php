<?php

namespace Modules\Tag\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Tag\Service\TagIndexService;

class TagIndexController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/tags",
     *     summary="Fetch all tags",
     *     description="Retrieve all tags, optionally filter by name",
     *     operationId="fetchTags",
     *     tags={"Tags"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", example="")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tags has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Smart TV"),
     *                     @OA\Property(property="url", type="string", example="smart-tv"),
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
    function __construct(private TagIndexService $tagIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $tags = $this->tagIndexService->index($request->get('name'));

        return $this->successResponse('Tags has been fetched successfully.', $tags);
    }
}
