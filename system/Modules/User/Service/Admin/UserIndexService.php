<?php

namespace Modules\User\Service\Admin;

use Illuminate\Pagination\Paginator;
use Modules\User\App\Models\User;

class UserIndexService
{
    function index($data)
    {
        $query = User::query();

        $query->when(isset($data['name']), function ($query) use ($data) {
            return $query->where('full_name', 'like', '%' . $data['name'] . '%')
                ->orWhere('email', 'like', '%' . $data['name'] . '%');
        });

        if (isset($data['name'])) {
            $page = 1;
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        $result =  $query->select('uuid', 'full_name', 'email', 'profile_picture', 'status')
            ->latest('created_at')
            ->paginate(20);

        $result->getCollection()->transform(function ($user) {
            return [
                'uuid' => $user->uuid,
                'fullName' => $user->full_name,
                'email' => $user->email,
                'profilePicture' => $user->profile_picture,
                'status' => User::$userStatus[$user->status],
            ];
        });

        return $result;
    }
}
