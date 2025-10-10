<?php

namespace Modules\Tag\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Tag\Service\TagShowService;

class TagShowController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/tags/show/{id}",
     *     summary="Show a tag",
     *     description="Show details of a tag by its ID",
     *     operationId="showTag",
     *     tags={"Tags"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Tag ID",
     *         @OA\Schema(
     *             type="string",
     *             example="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tag has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Smart TV"),
     *                 @OA\Property(property="url", type="string", example="smart-tv")
     *             )
     *         )
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
    function __construct(private TagShowService $tagShowService)
    {
    }

    function __invoke(int $id)
    {
        $tag = $this->tagShowService->show($id);

        return $this->successResponse('Tag has been fetched successfully.', $tag);
    }
}
