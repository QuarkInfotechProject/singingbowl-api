<?php

namespace Modules\Review\App\Http\Controllers\User\Question;

use Modules\Review\App\Http\Requests\Question\QuestionCreateRequest;
use Modules\Review\Service\User\Question\QuestionCreateService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class QuestionCreateController extends UserBaseController
{
    function __construct(private QuestionCreateService $questionCreateService)
    {
    }

    function __invoke(QuestionCreateRequest $request)
    {
        $this->questionCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('Question has been submitted successfully.');
    }
}
