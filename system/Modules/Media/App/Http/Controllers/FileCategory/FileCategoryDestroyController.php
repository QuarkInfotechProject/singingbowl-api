<?php

namespace Modules\Media\App\Http\Controllers\FileCategory;

use Illuminate\Http\Request;
use Modules\Media\Service\FileCategory\FileCategoryDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FileCategoryDestroyController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/media-categories/destroy",
     *     summary="Delete a media category",
     *     description="Delete a media category by its url",
     *     operationId="deleteMediaCategory",
     *     tags={"Media Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="url", type="string", example="rock")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Media category has been deleted successfully."))
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
    function __construct(private FileCategoryDestroyService $mediaCategoryDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->mediaCategoryDestroyService->destroy($request->get('url'), $request->getClientIp());

        return $this->successResponse('Media category has been deleted successfully.');
    }
}
