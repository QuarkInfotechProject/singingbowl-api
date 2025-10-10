<?php

namespace Modules\Color\App\Http\Controllers;

use Modules\Color\Service\ColorIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ColorIndexController extends AdminBaseController
{
    /**
     * @OA\Get(
     *     path="/api/admin/colors",
     *     summary="Fetch all colors",
     *     description="Retrieve all available colors",
     *     operationId="fetchColors",
     *     tags={"Colors"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Colors has been fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Red"),
     *                     @OA\Property(property="hexCode", type="string", example="#FF0000"),
     *                     @OA\Property(property="status", type="integer", example=1),
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
    function __construct(private ColorIndexService $colorIndexService)
    {
    }

    function __invoke()
    {
        $colors = $this->colorIndexService->index();

        return $this->successResponse('Colors has been fetched successfully.', $colors);
    }
}
