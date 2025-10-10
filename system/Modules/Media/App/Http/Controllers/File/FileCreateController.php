<?php

namespace Modules\Media\App\Http\Controllers\File;

use Modules\Media\App\Http\Requests\File\FileCreateRequest;
use Modules\Media\Service\File\FileCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FileCreateController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/files/create",
     *     summary="Create a media file",
     *     description="Create a new media file by uploading it with the provided file and file category ID (optional)",
     *     operationId="createMediaFile",
     *     tags={"Media Files"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"files[]"},
     *                 @OA\Property(
     *                     property="files[]",
     *                     type="string",
     *                     format="binary",
     *                     description="The file to upload"
     *                 ),
     *                 @OA\Property(property="fileCategoryId", type="string", nullable=true, description="ID of the file category")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="File has been created successfully."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private FileCreateService $fileCreateService)
    {
    }

    function __invoke(FileCreateRequest $request)
    {
        $this->fileCreateService->create($request, $request->getClientIp());

        return $this->successResponse('File has been created successfully.');
    }
}
