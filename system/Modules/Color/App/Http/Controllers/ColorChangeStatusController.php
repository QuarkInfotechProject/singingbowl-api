<?php

namespace Modules\Color\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Color\Service\ColorChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ColorChangeStatusController extends AdminBaseController
{
    /**
     * @OA\Post(
     *     path="/api/admin/colors/change-status",
     *     summary="Change status of a color",
     *     description="Change the status of a color by providing its ID",
     *     operationId="changeColorStatus",
     *     tags={"Colors"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Status has been changed successfully."))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Color not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Color not found."))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function __construct(private ColorChangeStatusService $colorChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->colorChangeStatusService->changeStatus($request->get('id'));

        return $this->successResponse('Status has been changed successfully.');
    }
}
