<?php

namespace Modules\Transaction\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Transaction\Service\TransactionIndexService;

class TransactionIndexController extends AdminBaseController
{
    function __construct(private TransactionIndexService $transactionIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $transactions = $this->transactionIndexService->index($request->all());

        return $this->successResponse('Transactions has been fetched successfully.', $transactions);
    }
}
