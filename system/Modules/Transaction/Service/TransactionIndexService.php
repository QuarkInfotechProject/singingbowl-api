<?php

namespace Modules\Transaction\Service;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Modules\Transaction\App\Models\Transaction;

class TransactionIndexService
{
    function index(array $data)
    {
        $query = Transaction::query();

        if (isset($data['transactionCode']) || isset($data['paymentMethod'])) {
            $page = 1;
            Paginator::currentPageResolver(fn() => $page);
        }

        if (isset($data['transactionCode'])) {
            $query->where('transaction_code', 'like', '%' . $data['transactionCode'] . '%');
        }

        if (isset($data['paymentMethod'])) {
            $query->where('payment_method', $data['paymentMethod']);
        }

        $results = $query->latest('created_at')
            ->select( 'order_id', 'transaction_code', 'payment_method', 'status', 'created_at')
            ->paginate(25);

        $results->getCollection()->transform(function ($result) {
            return [
                'orderId' => $result->order_id,
                'transactionCode' => $result->transaction_code,
                'paymentMethod' => $result->payment_method,
                'status' => $result->status,
                'createdAt' => Carbon::parse($result->created_at)->diffForHumans(),
            ];
        });

        return $results;
    }
}
