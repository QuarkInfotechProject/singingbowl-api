<?php

namespace Modules\Media\App\Http\Controllers\File;

use Illuminate\Http\Request;
use Modules\Media\Service\File\FileUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FileUpdateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/files/update",
     *     summary="Update a media file",
     *     description="Update details of a media file by its ID",
     *     operationId="updateMediaFile",
     *     tags={"Media Files"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property(property="id", type="string", example="1"),
     *                 @OA\Property(property="fileName", type="string", example="yellow"),
     *                 @OA\Property(property="fileCategoryId", type="string", nullable=true, example="2", description="ID of the file category")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="File has been updated successfully."))
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
    function __construct(private FileUpdateService $fileUpdateService)
    {
    }

    function __invoke(Request $request)
    {
        $this->fileUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('File has been updated successfully.');
    }
}
