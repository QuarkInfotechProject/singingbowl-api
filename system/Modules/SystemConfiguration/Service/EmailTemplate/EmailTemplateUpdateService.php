<?php

namespace Modules\SystemConfiguration\Service\EmailTemplate;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;
use Modules\SystemConfiguration\DTO\UpdateEmailTemplateDTO;

class EmailTemplateUpdateService
{
    function update(UpdateEmailTemplateDTO $updateEmailTemplateDTO, $ipAddress)
    {
        $emailTemplate = EmailTemplate::where('name', $updateEmailTemplateDTO->name)->first();

        if (!$emailTemplate) {
            throw new Exception('Email template not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            $emailTemplate->update([
                'subject' => $updateEmailTemplateDTO->subject,
                'message' => $updateEmailTemplateDTO->message
            ]);

            DB::commit();
        } catch(\Exception $exception) {
            Log::error('Error updating email template: ' . $exception->getMessage(), [
                'exception' => $exception,
                'updateEmailTemplateDTO' => $updateEmailTemplateDTO,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Email template updated of title: ' . $emailTemplate['title'],
                $emailTemplate->id,
                ActivityTypeConstant::EMAIL_UPDATED,
                $ipAddress)
        );
    }
}
