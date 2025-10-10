<?php

namespace Modules\Media\App\Http\Controllers\File;

use Illuminate\Http\Request;
use Modules\Media\Service\File\FileIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FileIndexController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/files",
     *     summary="Fetch all media files",
     *     description="Retrieve all media files with optional filtering and sorting",
     *     operationId="fetchMediaFiles",
     *     tags={"Media Files"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="grouped", type="string", description="Grouped by"),
     *                 @OA\Property(property="fileCategoryId", type="string", description="ID of the file category"),
     *                 @OA\Property(property="fileName", type="string", description="Name of the file to filter"),
     *                 @OA\Property(property="sortBy", type="string", description="Field to sort by"),
     *                 @OA\Property(property="sortDirection", type="string", enum={"asc", "desc"}, description="Sort direction")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Files has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="fileName", type="string", example="burger.jpg"),
     *                     @OA\Property(property="width", type="integer", example=1057),
     *                     @OA\Property(property="height", type="integer", example=980),
     *                     @OA\Property(property="url", type="string", example="http://localhost:98/files/070330170320246iELaA.jpg"),
     *                     @OA\Property(property="thumbnailUrl", type="string", example="http://localhost:98/files/Thumbnail/070330170320246iELaA.jpg")
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
    function __construct(private FileIndexService $fileIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $files = $this->fileIndexService->index($request->all());

        return $this->successResponse('Files has been fetched successfully.', $files);
    }
}
