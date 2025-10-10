<?php

namespace Modules\User\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;
use Modules\User\App\Events\SendActivateMail;
use Modules\User\App\Events\SendRegisterMail;
use Modules\User\App\Models\User;
use Modules\User\DTO\SendRegisterEmailDTO;

class UserActivateService
{
    function activate($data, string $ipAddress)
    {
        $validator = Validator::make($data, [
            'remarks' => 'required|min:2|max:500'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $endUser = User::where('uuid', $data['uuid'])->first();

        if (!$endUser) {
            throw new Exception('User not found.', ErrorCode::NOT_FOUND);
        }

        if ($endUser->status === User::STATUS_ACTIVE) {
            throw new Exception('User is already in active state.', ErrorCode::BAD_REQUEST);
        }


        try {
            DB::beginTransaction();

            $endUser->update([
                'status' =>User::STATUS_ACTIVE,
                'remarks' => $data['remarks']
            ]);

            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'End user activated of name: ' . $endUser->full_name,
                    $endUser->id,
                    ActivityTypeConstant::USER_ACTIVATED,
                    $ipAddress
                )
            );

            $this->sendEmail($endUser);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error during user activate: ' . $exception->getMessage(), [
                'exception' => $exception,
                'userName' => $endUser->full_name
            ]);
            DB::rollBack();
        }
    }

    private function sendEmail($endUser)
    {
        $template = EmailTemplate::where('name', 'user_activated')->first();

        $message = strtr($template->message, [
            '{FULLNAME}' => $endUser->full_name,
            '{EMAIL}' => $endUser->email
        ]);

        $sendEmailDTO = SendRegisterEmailDTO::from([
            'title' => $template->title,
            'subject' => $template->subject,
            'description' => $message,
            'email' => $endUser->email,
        ]);

        Event::dispatch(new SendActivateMail($sendEmailDTO));
    }
}
