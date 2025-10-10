<?php

namespace Modules\SystemConfiguration\Service\EmailTemplate;

use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;

class EmailTemplateShowService
{
    function show(string $name)
    {
        $emailTemplate =  EmailTemplate::select('title', 'subject', 'message', 'description')
            ->where('name', $name)
            ->first();

        if (!$emailTemplate) {
            throw new Exception('Email template not found.', ErrorCode::NOT_FOUND);
        }

        return $emailTemplate;
    }
}
