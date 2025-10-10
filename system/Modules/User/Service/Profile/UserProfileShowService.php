<?php

namespace Modules\User\Service\Profile;

use Illuminate\Support\Facades\Auth;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class UserProfileShowService
{
    function show()
    {
        if (!Auth::check()) {
            throw new Exception('User not authenticated.', ErrorCode::UNAUTHORIZED);
        }

        $user = Auth::user()->only([
            'full_name',
            'phone_no',
            'email',
            'date_of_birth',
            'gender',
            'offers_notification',
            'profile_picture',
        ]);

        return [
            'fullName' => $user['full_name'],
            'phone' => $user['phone_no'] ?? '',
            'email' => $user['email'],
            'dateOfBirth' => $user['date_of_birth'] ?? '',
            'gender' => $user['gender'] ?? '',
            'offersNotification' => $user['offers_notification'],
            'profilePicture' => $user['profile_picture'] ?? '',
        ];
    }
}
