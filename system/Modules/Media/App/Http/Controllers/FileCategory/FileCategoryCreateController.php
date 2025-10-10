<?php

namespace Modules\Media\App\Http\Controllers\FileCategory;

use Modules\Media\App\Http\Requests\FileCategory\FileCategoryCreateRequest;
use Modules\Media\Service\FileCategory\FileCategoryCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FileCategoryCreateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/media-categories/create",
     *     summary="Create a media category",
     *     description="Create a new media category with the provided name and slug",
     *     operationId="createMediaCategory",
     *     tags={"Media Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example=""),
     *                 @OA\Property(property="url", type="string", example="")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Media category has been created successfully."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private FileCategoryCreateService $fileCategoryCreateService)
    {
    }

    function __invoke(FileCategoryCreateRequest $request)
    {
        $this->fileCategoryCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('Media category has been created successfully.');
    }
}
