<?php

namespace Modules\Tag\App\Http\Controllers;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Tag\App\Http\Requests\TagCreateRequest;
use Modules\Tag\Service\TagCreateService;

class TagCreateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/tags/create",
     *     summary="Create a tag",
     *     description="Create a new tag with the provided name and URL",
     *     operationId="createTag",
     *     tags={"Tags"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "url"},
     *                 @OA\Property(property="name", type="string", example="Smart TV"),
     *                 @OA\Property(property="url", type="string", example="smart-tv")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Tag has been created successfully."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private TagCreateService $tagCreateService)
    {
    }

    function __invoke(TagCreateRequest $request)
    {
        $this->tagCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('Tag has been created successfully.');
    }
}
