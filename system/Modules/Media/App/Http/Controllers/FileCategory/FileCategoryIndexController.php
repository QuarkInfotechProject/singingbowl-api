<?php

namespace Modules\Media\App\Http\Controllers\FileCategory;

use Modules\Media\Service\FileCategory\FileCategoryIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FileCategoryIndexController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/media-categories",
     *     summary="Fetch all media categories",
     *     description="Retrieve all media categories along with their details",
     *     operationId="getMediaCategories",
     *     tags={"Media Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Media category has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Rock"),
     *                     @OA\Property(property="url", type="string", example="rock")
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
    function __construct(private FileCategoryIndexService $mediaCategoryIndexService)
    {
    }

    function __invoke()
    {
        $mediaCategories = $this->mediaCategoryIndexService->index();

        return $this->successResponse('Media categories has been fetched successfully.', $mediaCategories);
    }
}
