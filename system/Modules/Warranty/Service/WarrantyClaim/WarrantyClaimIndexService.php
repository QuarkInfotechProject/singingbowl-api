<?php

namespace Modules\Warranty\Service\WarrantyClaim;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Modules\Warranty\App\Models\WarrantyClaim;

class WarrantyClaimIndexService
{
    function index($data)
    {
        if (isset($data['name']) || isset($data['email']) || isset($data['phone']) ||isset($data['product'])) {
            $page = 1;
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        $query = WarrantyClaim::query();

        $query->when(isset($data['name']), function ($query) use ($data) {
            return $query->where('name', 'like', '%' . $data['name'] . '%');
        });

        $query->when(isset($data['email']), function ($query) use ($data) {
            return $query->where('email', $data['email']);
        });

        $query->when(isset($data['phone']), function ($query) use ($data) {
            return $query->where('phone', $data['phone']);
        });

        $query->when(isset($data['product']), function ($query) use ($data) {
            return $query->where('product_name', $data['product']);
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
