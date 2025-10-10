<?php

namespace Modules\OrderProcessing\Service;

use Modules\OrderProcessing\App\Models\OrderArtifact;

class OrderArtifactsIndexService
{
    function index()
    {
        try {
            $orderArtifacts = OrderArtifact::orderBy('date', 'desc')->take(5)->get();

            return $orderArtifacts->transform(function ($orderArtifact) {
                return [
                    'date' => $orderArtifact->date,
                    'fileName' => $orderArtifact->file_name,
                    'OrderCount' => $orderArtifact->order_count,
                    'shippingCompany' => $orderArtifact->shipping_company,
                    'filePath' => asset(('modules/orderArtifacts/') . $orderArtifact->file_name),
                ];
            });
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
