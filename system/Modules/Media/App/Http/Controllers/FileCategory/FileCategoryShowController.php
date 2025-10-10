<?php

namespace Modules\Media\App\Http\Controllers\FileCategory;

use Illuminate\Http\Request;
use Modules\Media\Service\FileCategory\FileCategoryShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FileCategoryShowController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/media-categories/show/{slug}",
     *     summary="Show a media category",
     *     description="Show a media category by its url",
     *     operationId="showMediaCategory",
     *     tags={"Media Categories"},
     *     @OA\Parameter(
     *         name="url",
     *         in="path",
     *         required=true,
     *         description="Url of the media category",
     *         @OA\Schema(
     *             type="string",
     *             example="rock"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Media category has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Rock"),
     *                 @OA\Property(property="url", type="string", example="rock")
     *             )
     *         )
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
    function __construct(private FileCategoryShowService $mediaCategoryShowService)
    {
    }

    function __invoke(string $slug)
     {
         $mediaCategory = $this->mediaCategoryShowService->show($slug);

         return $this->successResponse('Media category has been fetched successfully.', $mediaCategory);
     }
}
