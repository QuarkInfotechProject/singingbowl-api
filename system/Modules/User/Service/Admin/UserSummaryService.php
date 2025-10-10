<?php

namespace Modules\User\Service\Admin;

use Carbon\Carbon;
use Modules\User\App\Models\User;

class UserSummaryService
{
    function index($name = '', $sortBy = 'last_active', $sortDirection = 'desc', $page = 1, $perPage = 25)
    {
        $validSortFields = [
            'name' => 'full_name',
            'last_active' => 'last_active_at',
            'date_registered' => 'created_at',
            'orders' => 'orders_count',
            'total_spend' => 'orders_sum_total'
        ];

        $sortColumn = $validSortFields[$sortBy] ?? 'last_active_at';

        $users = User::with(['orders', 'address'])
            ->select('id', 'uuid', 'full_name', 'email', 'last_active_at', 'created_at')
            ->when($name, function ($query) use ($name) {
                $query->where('full_name', 'like', "%{$name}%");
            })
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage, ['*'], 'page', $page);

        $totalCustomers = $users->total();
        $totalOrders = $users->sum('orders_count');
        $totalRevenue = $users->sum('orders_sum_total');

        $averageOrdersPerCustomer = $totalCustomers > 0 ? $totalOrders / $totalCustomers : 0;
        $averageLifetimeSpend = $totalCustomers > 0 ? $totalRevenue / $totalCustomers : 0;
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $formattedUsers = $users->getCollection()->map(function ($user) {
            $aov = $user->orders_count > 0 ? $user->orders_sum_total / $user->orders_count : 0;

            $addressDetails = $user->address ? [
                'location' => $user->address->address,
                'zone' => $user->address->zone_name,
                'city' => $user->address->city_name,
                'province' => $user->address->province_name,
                'country' => $user->address->country_name,
            ] : [];

            return [
                'id' => $user->uuid,
                'name' => $user->full_name,
                'email' => $user->email,
                'lastActive' => $user->last_active_at
                    ? Carbon::parse($user->last_active_at)->format('d-m-Y')
                    : 'N/A',
                'dateRegistered' => Carbon::parse($user->created_at)->format('d-m-Y'),
                'orders' => $user->orders_count,
                'total' => $user->orders_sum_total ?? 0,
                'aov' => round($aov, 2),
                'address' => $addressDetails,
            ];
        });

        return [
            'summary' => [
                'totalCustomers' => $totalCustomers,
                'averageOrdersPerCustomer' => round($averageOrdersPerCustomer, 4),
                'averageLifetimeSpend' => round($averageLifetimeSpend, 2),
                'averageOrderValue' => round($averageOrderValue, 2),
            ],
            'users' => $formattedUsers,
            'pagination' => [
                'currentPage' => $users->currentPage(),
                'lastPage' => $users->lastPage(),
                'perPage' => $users->perPage(),
                'total' => $users->total(),
            ]
        ];
    }
}
