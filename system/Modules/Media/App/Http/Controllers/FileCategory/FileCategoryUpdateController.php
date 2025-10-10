<?php

namespace Modules\Media\App\Http\Controllers\FileCategory;

use Modules\Media\App\Http\Requests\FileCategory\FileCategoryUpdateRequest;
use Modules\Media\Service\FileCategory\FileCategoryUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FileCategoryUpdateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/media-categories/update",
     *     summary="Update a media category",
     *     description="Update a media category with the provided name and slug",
     *     operationId="updateMediaCategory",
     *     tags={"Media Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Watch"),
     *                 @OA\Property(property="url", type="string", example="watch")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Media category has been updated successfully."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Media category not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Media category not found."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private FileCategoryUpdateService $mediaCategoryUpdateService)
    {
    }

    function __invoke(FileCategoryUpdateRequest $request)
    {
        $this->mediaCategoryUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Media category has been updated successfully.');
    }
}
