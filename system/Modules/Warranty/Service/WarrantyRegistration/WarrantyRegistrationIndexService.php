<?php

namespace Modules\Warranty\Service\WarrantyRegistration;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Modules\Warranty\App\Models\WarrantyRegistration;

class WarrantyRegistrationIndexService
{
    function index($data)
    {
        if (isset($data['name']) || isset($data['email'])) {
            $page = 1;
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        $query = WarrantyRegistration::query();

        $query->when(isset($data['name']), function ($query) use ($data) {
            return $query->where('name', 'like', '%' . $data['name'] . '%');
        });

        $query->when(isset($data['email']), function ($query) use ($data) {
            return $query->where('email', $data['email']);
        });

        $results =  $query->select('id', 'name', 'email', 'phone', 'product_name as product', 'created_at')
            ->latest()
            ->paginate(30);

        $results->getCollection()->transform(function ($result) {
            return [
                'id' => $result->id,
                'name' => $result->name,
                'email' => $result->email,
                'submittedAt' => Carbon::parse($result->created_at)->isoFormat('Do MMMM,YYYY @ h:mm A')
            ];
        });

        return $results;
    }
}
