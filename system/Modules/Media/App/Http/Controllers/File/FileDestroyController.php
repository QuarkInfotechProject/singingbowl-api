<?php

namespace Modules\Media\App\Http\Controllers\File;

use Illuminate\Http\Request;
use Modules\Media\Service\File\FileDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FileDestroyController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/files/destroy",
     *     summary="Delete a media file",
     *     description="Delete a media file by its ID",
     *     operationId="deleteMediaFile",
     *     tags={"Media Files"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="id", type="string", example="8")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="File has been deleted successfully."))
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
    function __construct(private FileDestroyService $fileDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->fileDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('File has been deleted successfully.');
    }
}
