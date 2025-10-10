<?php

namespace Modules\Media\App\Http\Controllers\File;

use Illuminate\Http\Request;
use Modules\Media\Service\File\FileShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FileShowController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/files/show/{id}",
     *     summary="Show a media file",
     *     description="Show details of a media file by its ID",
     *     operationId="showMediaFile",
     *     tags={"Media Files"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the media file",
     *         @OA\Schema(
     *             type="string",
     *             example="2"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="File has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="file", type="string", example="orange.jpg"),
     *                 @OA\Property(property="filename", type="string", example="orange"),
     *                 @OA\Property(property="fileCategoryId", type="string", nullable=true),
     *                 @OA\Property(property="fileCategoryName", type="string", example="Ungrouped"),
     *                 @OA\Property(property="size", type="string", example="295.6 KB"),
     *                 @OA\Property(property="width", type="integer", example=800),
     *                 @OA\Property(property="height", type="integer", example=1200),
     *                 @OA\Property(property="url", type="string", example="http://localhost:98/files/06371917032024yXMC4A.jpg"),
     *                 @OA\Property(property="thumbnailUrl", type="string", example="http://localhost:98/files/Thumbnail/06371917032024yXMC4A.jpg"),
     *                 @OA\Property(property="createdAt", type="string", format="date-time", example="2024-03-17 06:37:20")
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
     *         description="File not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="File not found."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private FileShowService $fileShowService)
    {
    }

    function __invoke(int $id)
    {
        $file = $this->fileShowService->show($id);

        return $this->successResponse('File has been fetched successfully.', $file);
    }
}
