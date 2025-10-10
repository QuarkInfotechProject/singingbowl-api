<?php

namespace Modules\User\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\SystemConfig;
use Modules\User\App\Models\User;
use Modules\User\App\Models\VerificationCode;

class UserResetPasswordService
{
    function resetPassword($data)
    {
        try {
            $verification = VerificationCode::where('email', $data['email'])->latest()->first();

            if (!$verification) {
                throw new Exception('Invalid OTP or email.', ErrorCode::NOT_FOUND);
            }

            $codeAttempts = SystemConfig::firstWhere('name', 'code_attempts')->value('value');

            if ($verification->attempts >= $codeAttempts) {
                throw new Exception('Too many verification code attempts.', ErrorCode::TOO_MANY_REQUESTS);
            }

            if ($verification->expires_at < now()) {
                throw new Exception('Verification code has already expired!', ErrorCode::GONE);
            }

            if ($verification->code != $data['OTP']) {
                $verification->attempts++;
                $verification->save();
                throw new Exception('Verification code doesn\'t match.', ErrorCode::BAD_REQUEST);
            }

            DB::transaction(function () use ($data, $verification) {
                User::where('email', $data['email'])->update([
                    'password' => bcrypt($data['password']),
                ]);
                $verification->delete();
            });

        } catch (\Exception $exception) {
            Log::error('Error during password reset: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data
            ]);
            throw $exception;
        }
    }
}
