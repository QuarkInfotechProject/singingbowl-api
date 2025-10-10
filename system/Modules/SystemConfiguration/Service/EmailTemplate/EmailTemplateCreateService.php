<?php

namespace Modules\SystemConfiguration\Service\EmailTemplate;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\SystemConfiguration\App\Models\EmailTemplate;
use Modules\SystemConfiguration\DTO\EmailTemplateDTO;

class EmailTemplateCreateService
{
    function create(EmailTemplateDTO $emailTemplateDTO, $updateOption)
    {
        try {
            DB::beginTransaction();

            $existingTemplate = EmailTemplate::where('name', $emailTemplateDTO->name)->first();

            if ($existingTemplate) {
                if ($updateOption) {
                    $existingTemplate->title = $emailTemplateDTO->title;
                    $existingTemplate->subject = $emailTemplateDTO->subject;
                    $existingTemplate->message = $emailTemplateDTO->message;
                    $existingTemplate->description = $emailTemplateDTO->description;
                    $existingTemplate->image = $emailTemplateDTO->image;
                    $existingTemplate->save();
                }
            } else {
                $newTemplate = new EmailTemplate();
                $newTemplate->name = $emailTemplateDTO->name;
                $newTemplate->title = $emailTemplateDTO->title;
                $newTemplate->subject = $emailTemplateDTO->subject;
                $newTemplate->message = $emailTemplateDTO->message;
                $newTemplate->description = $emailTemplateDTO->description;
                $newTemplate->image = $emailTemplateDTO->image;
                $newTemplate->save();
            }

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error creating or updating email template: ' . $exception->getMessage(), [
                'exception' => $exception,
                'emailTemplateDTO' => $emailTemplateDTO,
                'updateOption' => $updateOption
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
