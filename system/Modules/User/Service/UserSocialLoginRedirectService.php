<?php

namespace Modules\User\Service;

use Laravel\Socialite\Facades\Socialite;
use Modules\Shared\Constant\SocialMediaConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class UserSocialLoginRedirectService
{
    function handleSocialLoginRedirect(string $provider)
    {
        if ($provider == SocialMediaConstant::GOOGLE) {
            return Socialite::driver(SocialMediaConstant::GOOGLE)->stateless()->redirect()->getTargetUrl();
        } else {
            throw new Exception('Social Provider not found.', ErrorCode::BAD_REQUEST);
        }
    }
}
